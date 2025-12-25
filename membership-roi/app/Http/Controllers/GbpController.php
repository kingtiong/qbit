<?php

namespace App\Http\Controllers;

use App\Models\FoundingPartnerPurchase;
use App\Models\GbpPurchase;
use App\Models\GbpTier;
use App\Models\Setting;
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
    private const FOUNDING_PARTNER_CAP = 30;

    public function index(): View
    {
        $user = Auth::user();
        $registeredWallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);

        $foundingSold = (int) (Setting::getValue('qbp_founding_partner_sold', '0') ?: '0');
        $qbpUnlocked = $foundingSold >= self::FOUNDING_PARTNER_CAP;
        $myFounding = FoundingPartnerPurchase::query()->where('user_id', $user->id)->first();

        /** @var \Illuminate\Database\Eloquent\Collection<int, GbpTier> $tiers */
        $tiers = collect();
        if ($qbpUnlocked) {
            $tiers = GbpTier::query()
                ->where('is_active', true)
                // Always compute sold from actual purchases (MySQL truth),
                // so UI remains correct even if sold_units was ever reset.
                ->withSum('purchases', 'units')
                ->orderBy('tier')
                ->get();
        }

        $tierRows = [];
        foreach ($tiers as $t) {
            $total = (int) ($t->total_units ?? 0);
            $sold = (int) ($t->purchases_sum_units ?? $t->sold_units ?? 0);
            $remaining = max(0, $total - $sold);
            $tierRows[] = [
                'id' => (int) $t->id,
                'tier' => (int) $t->tier,
                'unit_price' => (int) $t->unit_price,
                'total_units' => $total,
                'sold_units' => $sold,
                'remaining_units' => $remaining,
            ];
        }

        $currentTier = null;
        $nextTier = null;
        $finalTier = null;
        foreach ($tierRows as $idx => $row) {
            if ($finalTier === null || $row['tier'] > $finalTier['tier']) {
                $finalTier = $row;
            }
            if ($currentTier === null && $row['remaining_units'] > 0) {
                $currentTier = $row;
                $nextTier = $tierRows[$idx + 1] ?? null;
            }
        }
        if ($currentTier === null) {
            // Sold out: current becomes final (for display).
            $currentTier = $finalTier;
            $nextTier = null;
        }

        $displayTiers = [];
        foreach ([$currentTier, $nextTier, $finalTier] as $row) {
            if (!$row) {
                continue;
            }
            $displayTiers[$row['tier']] = $row; // de-dupe by tier
        }
        ksort($displayTiers);

        $currentUnitPrice = (int) (($currentTier['unit_price'] ?? 0) ?: ($finalTier['unit_price'] ?? 0));

        $myPurchases = collect();
        if ($qbpUnlocked) {
            $myPurchases = GbpPurchase::query()
                ->where('user_id', $user->id)
                ->orderByDesc('purchased_at')
                ->limit(20)
                ->get();
        }

        return view('gbp.index', [
            'user' => $user,
            'registeredWallet' => $registeredWallet,
            'tiers' => array_values($displayTiers),
            'currentTier' => $currentTier,
            'nextTier' => $nextTier,
            'finalTier' => $finalTier,
            'currentUnitPrice' => $currentUnitPrice,
            'myPurchases' => $myPurchases,
            'foundingCap' => self::FOUNDING_PARTNER_CAP,
            'foundingSold' => $foundingSold,
            'qbpUnlocked' => $qbpUnlocked,
            'myFounding' => $myFounding,
        ]);
    }

    public function purchase(Request $request): RedirectResponse
    {
        $foundingSold = (int) (Setting::getValue('qbp_founding_partner_sold', '0') ?: '0');
        if ($foundingSold < self::FOUNDING_PARTNER_CAP) {
            return back()->with('status', 'QBP is locked. Complete Founding Partners (30/30) first.');
        }

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

    public function purchaseFoundingPartner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package' => ['required', 'in:pro,pro_max'],
        ]);

        $user = Auth::user();
        $package = (string) $validated['package'];
        $amount = $package === FoundingPartnerPurchase::PACKAGE_PRO_MAX ? '10000.00' : '5000.00';

        $wallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);

        try {
            DB::transaction(function () use ($user, $wallet, $package, $amount): void {
                // Lock the gate counter row so the 30-cap is enforced safely under concurrency.
                $gate = Setting::query()
                    ->where('key', 'qbp_founding_partner_sold')
                    ->lockForUpdate()
                    ->first();

                if (!$gate) {
                    $gate = Setting::create(['key' => 'qbp_founding_partner_sold', 'value' => '0']);
                }

                $sold = (int) ($gate->value ?? '0');
                if ($sold >= self::FOUNDING_PARTNER_CAP) {
                    abort(422, 'Founding Partners are sold out. QBP is now open.');
                }

                // Each user can only buy one (either Pro or Pro Max).
                $already = FoundingPartnerPurchase::query()
                    ->where('user_id', $user->id)
                    ->exists();
                if ($already) {
                    abort(422, 'You have already purchased a Founding Partner slot.');
                }

                /** @var Wallet $lockedWallet */
                $lockedWallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

                if (bccomp((string) $lockedWallet->balance, (string) $amount, 2) < 0) {
                    abort(422, 'Insufficient Registered Wallet balance.');
                }

                $occurredOn = BusinessTime::today()->toDateString();
                $purchasedAt = Carbon::now();

                $tx = $lockedWallet->transactions()->create([
                    'type' => 'founding_partner_purchase_debit',
                    'amount' => bcmul($amount, '-1', 2),
                    'meta' => [
                        'package' => $package,
                        'cap' => self::FOUNDING_PARTNER_CAP,
                    ],
                    'occurred_on' => $occurredOn,
                ]);

                FoundingPartnerPurchase::create([
                    'user_id' => $user->id,
                    'package' => $package,
                    'amount' => $amount,
                    'wallet_transaction_id' => $tx->id,
                    'purchased_at' => $purchasedAt,
                ]);

                $lockedWallet->decrement('balance', $amount);

                // Keep the gate counter in sync.
                $gate->value = (string) ($sold + 1);
                $gate->save();
            });
        } catch (\Throwable $e) {
            $msg = $e->getMessage() ?: 'Unable to purchase Founding Partner right now.';
            return back()->with('status', $msg);
        }

        return back()->with('status', 'Founding Partner purchased successfully.');
    }
}

