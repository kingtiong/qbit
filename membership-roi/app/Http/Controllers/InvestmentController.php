<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentPackage;
use App\Models\SalesEvent;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\BusinessTime;
use App\Services\EarningAllocator;
use App\Services\RankRules;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $registeredWallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);
        $commissionWallet = Wallet::forUser($user->id, Wallet::TYPE_COMMISSION);

        $investments = Investment::query()
            ->where('user_id', $user->id)
            ->with('package')
            ->orderByDesc('id')
            ->get();

        $walletIds = array_values(array_filter([$registeredWallet->id ?? null, $commissionWallet->id ?? null]));
        $recentTransactions = WalletTransaction::query()
            ->with('wallet')
            ->whereIn('wallet_id', $walletIds)
            ->orderByDesc('occurred_on')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $totalInvested = '0.00';
        $totalEarned = '0.00';
        $totalMaxReturn = '0.00';
        $activeCount = 0;
        foreach ($investments as $inv) {
            $totalInvested = bcadd($totalInvested, (string) ($inv->amount ?? '0.00'), 2);
            $totalEarned = bcadd($totalEarned, (string) ($inv->total_earned ?? '0.00'), 2);
            $totalMaxReturn = bcadd($totalMaxReturn, (string) ($inv->max_return_amount ?? '0.00'), 2);
            if ($inv->status === 'active') {
                $activeCount++;
            }
        }
        $totalRemaining = bcsub($totalMaxReturn, $totalEarned, 2);
        if (bccomp($totalRemaining, '0', 2) < 0) {
            $totalRemaining = '0.00';
        }

        return view('dashboard', [
            'user' => $user,
            'registeredWallet' => $registeredWallet,
            'commissionWallet' => $commissionWallet,
            'investments' => $investments,
            'recentTransactions' => $recentTransactions,
            'summary' => [
                'active_count' => $activeCount,
                'total_invested' => $totalInvested,
                'total_earned' => $totalEarned,
                'total_max_return' => $totalMaxReturn,
                'total_remaining' => $totalRemaining,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investment_package_id' => ['required', 'integer', 'exists:investment_packages,id'],
            'wallet_type' => ['required', 'in:registered,commission'],
        ]);

        $user = Auth::user();

        /** @var InvestmentPackage $package */
        $package = InvestmentPackage::query()
            ->whereKey($validated['investment_package_id'])
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($user, $package, $validated): void {
            $businessDate = BusinessTime::today();

            // Enforce: max 10 active QPU per user at the beginning.
            $activeCount = Investment::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->count();
            if ($activeCount >= 10) {
                abort(422, 'You can only run up to 10 active QPU at a time.');
            }

            // Enforce package unit inventory (except admin purchase).
            /** @var InvestmentPackage $pkgLocked */
            $pkgLocked = InvestmentPackage::query()->whereKey($package->id)->lockForUpdate()->firstOrFail();
            $isAdminPurchase = (bool) ($user->is_admin ?? false);
            if (!$isAdminPurchase) {
                $remaining = max(0, (int) ($pkgLocked->total_units ?? 0) - (int) ($pkgLocked->sold_units ?? 0));
                if ($remaining <= 0) {
                    abort(422, 'This QPU package is sold out.');
                }
            }

            // Purchases can debit from Registered or Quant wallet.
            $walletType = $validated['wallet_type'] === 'commission'
                ? Wallet::TYPE_COMMISSION
                : Wallet::TYPE_REGISTERED;
            $wallet = Wallet::forUser($user->id, $walletType);
            $wallet->refresh();

            if (bccomp((string) $wallet->balance, (string) $package->amount, 2) < 0) {
                abort(422, 'Insufficient wallet balance. Please deposit first.');
            }

            $startedOn = $businessDate->toDateString();
            $maxReturnAmount = bcmul((string) $package->amount, (string) ($package->max_return_multiplier ?? '0'), 2);

            $investment = Investment::create([
                'user_id' => $user->id,
                'investment_package_id' => $package->id,
                'currency' => $package->currency,
                'amount' => $package->amount,
                'status' => 'active',
                'started_on' => $startedOn,
                'max_return_amount' => $maxReturnAmount,
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'investment_purchase_debit',
                'amount' => bcmul((string) $package->amount, '-1', 2),
                'meta' => [
                    'investment_id' => $investment->id,
                    'investment_package_id' => $package->id,
                    'package_code' => $package->code,
                    'wallet_type' => $walletType,
                ],
                'occurred_on' => $startedOn,
            ]);
            $wallet->decrement('balance', (string) $package->amount);

            SalesEvent::create([
                'user_id' => $user->id,
                'currency' => $package->currency,
                'amount' => $package->amount,
                'type' => 'investment_purchase',
                'occurred_on' => $startedOn,
                'meta' => [
                    'investment_id' => $investment->id,
                    'investment_package_id' => $package->id,
                    'package_code' => $package->code,
                    'wallet_type' => $walletType,
                ],
            ]);

            // Deduct 1 unit from package inventory (except admin purchase).
            if (!$isAdminPurchase) {
                $pkgLocked->increment('sold_units', 1);
            }

            // Direct sponsor commission is now based on the downline's investment amount (one-time on purchase).
            $sponsor = $user->sponsor()->first();
            if ($sponsor) {
                $pct = RankRules::directSponsorPercent((string) ($sponsor->rank ?? 'B'));
                if (bccomp($pct, '0', 5) > 0) {
                    $amt = bcmul((string) $package->amount, $pct, 2);
                    EarningAllocator::creditToOldestInvestments(
                        $sponsor->id,
                        $amt,
                        'direct_sponsor',
                        $businessDate,
                        [
                            'downline_user_id' => $user->id,
                            'investment_id' => $investment->id,
                            'investment_package_id' => $package->id,
                            'package_code' => $package->code,
                            'investment_amount' => (string) $package->amount,
                            'percent' => $pct,
                            'date' => $startedOn,
                        ],
                    );
                }
            }
        });

        return back()->with('status', 'Investment created.');
    }
}
