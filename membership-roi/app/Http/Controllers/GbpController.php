<?php

namespace App\Http\Controllers;

use App\Models\GbpPurchase;
use App\Models\GbpTier;
use App\Models\Wallet;
use App\Services\BusinessTime;
use App\Services\EarningAllocator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GbpController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $registeredWallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);

        $tiers = GbpTier::query()
            ->where('is_active', true)
            ->orderBy('tier')
            ->get();

        $myPurchases = GbpPurchase::query()
            ->where('user_id', $user->id)
            ->orderByDesc('purchased_at')
            ->limit(20)
            ->get();

        return view('gbp.index', [
            'user' => $user,
            'registeredWallet' => $registeredWallet,
            'tiers' => $tiers,
            'myPurchases' => $myPurchases,
        ]);
    }

    public function purchase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'units' => ['required', 'integer', 'min:1'],
        ]);

        $user = Auth::user();
        $unitsRequested = (int) $validated['units'];

        // GBP purchases use the Registered Wallet (USDT).
        $wallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);

        try {
            DB::transaction(function () use ($user, $wallet, $unitsRequested): void {
                /** @var Wallet $lockedWallet */
                $lockedWallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

                /** @var \Illuminate\Database\Eloquent\Collection<int, GbpTier> $tiers */
                $tiers = GbpTier::query()
                    ->where('is_active', true)
                    ->orderBy('tier')
                    ->lockForUpdate()
                    ->get();

                $totalRemaining = 0;
                foreach ($tiers as $t) {
                    $totalRemaining += max(0, (int) $t->total_units - (int) $t->sold_units);
                }

                if ($totalRemaining < $unitsRequested) {
                    abort(422, 'Not enough GBP units remaining.');
                }

                // Allocate purchase across tiers from oldest/cheapest to newest (tier 1 -> tier 31).
                $remainingToBuy = $unitsRequested;
                $lines = [];
                $totalCostInt = 0;

                foreach ($tiers as $tier) {
                    if ($remainingToBuy <= 0) {
                        break;
                    }
                    $available = max(0, (int) $tier->total_units - (int) $tier->sold_units);
                    if ($available <= 0) {
                        continue;
                    }

                    $take = min($available, $remainingToBuy);
                    $unitPrice = (int) $tier->unit_price;
                    $lineTotal = $take * $unitPrice; // integer, no cents

                    $lines[] = [
                        'gbp_tier' => $tier,
                        'tier' => (int) $tier->tier,
                        'units' => $take,
                        'unit_price' => $unitPrice,
                        'total' => $lineTotal,
                    ];

                    $totalCostInt += $lineTotal;
                    $remainingToBuy -= $take;
                }

                if ($remainingToBuy !== 0) {
                    abort(422, 'Unable to allocate GBP purchase. Please try again.');
                }

                // Wallet uses 2 decimals; GBP is integer-only so we debit as X.00
                $totalDebit = number_format((float) $totalCostInt, 2, '.', '');
                if (bccomp((string) $lockedWallet->balance, $totalDebit, 2) < 0) {
                    abort(422, 'Insufficient Registered Wallet balance.');
                }

                $occurredOn = BusinessTime::today()->toDateString();
                $purchasedAt = Carbon::now();

                $metaLines = [];
                foreach ($lines as $l) {
                    $metaLines[] = [
                        'tier' => $l['tier'],
                        'units' => $l['units'],
                        'unit_price' => $l['unit_price'],
                        'total' => $l['total'],
                    ];
                }

                $tx = $lockedWallet->transactions()->create([
                    'type' => 'gbp_purchase_debit',
                    'amount' => bcmul($totalDebit, '-1', 2),
                    'meta' => [
                        'units' => $unitsRequested,
                        'total_amount_int' => $totalCostInt,
                        'lines' => $metaLines,
                    ],
                    'occurred_on' => $occurredOn,
                ]);

                // Apply inventory + create purchase rows.
                foreach ($lines as $l) {
                    /** @var GbpTier $tier */
                    $tier = $l['gbp_tier'];
                    $tier->increment('sold_units', $l['units']);

                    GbpPurchase::create([
                        'user_id' => $user->id,
                        'gbp_tier_id' => $tier->id,
                        'units' => $l['units'],
                        'unit_price' => $l['unit_price'],
                        'total_amount' => $l['total'],
                        'wallet_transaction_id' => $tx->id,
                        'purchased_at' => $purchasedAt,
                    ]);
                }

                $lockedWallet->decrement('balance', $totalDebit);

                // Direct sponsor GBP commission (10%):
                // Sponsor must have bought at least 1 GBP, otherwise commission is 0.
                $sponsor = $user->sponsor()->first();
                if ($sponsor) {
                    $sponsorHasGbp = GbpPurchase::query()
                        ->where('user_id', $sponsor->id)
                        ->exists();

                    if ($sponsorHasGbp) {
                        // Integer-only commission: round to nearest whole USDT, then pay as X.00
                        $commissionInt = (int) round($totalCostInt * 0.10);
                        if ($commissionInt > 0) {
                            $commissionAmount = number_format((float) $commissionInt, 2, '.', '');
                            $date = BusinessTime::today();

                            // Credits into sponsor's commission wallet, respecting investment caps.
                            EarningAllocator::creditToOldestInvestments(
                                $sponsor->id,
                                $commissionAmount,
                                'gbp_direct_commission',
                                $date,
                                [
                                    'downline_user_id' => $user->id,
                                    'downline_units' => $unitsRequested,
                                    'downline_total_amount_int' => $totalCostInt,
                                    'percent' => '0.10',
                                    'registered_wallet_tx_id' => $tx->id,
                                    'date' => $date->toDateString(),
                                ],
                                'gbp_direct_commission_credit',
                            );
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            $msg = $e->getMessage() ?: 'Unable to purchase GBP right now.';
            return back()->with('status', $msg);
        }

        return back()->with('status', 'GBP purchased successfully.');
    }
}

