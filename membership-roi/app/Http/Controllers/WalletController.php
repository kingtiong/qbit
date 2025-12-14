<?php

namespace App\Http\Controllers;

use App\Models\DepositAddress;
use App\Models\DepositSession;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use App\Services\BusinessTime;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $activeSession = DepositSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('reserved_until', '>', now())
            ->with('depositAddress')
            ->latest('id')
            ->first();

        $recentDeposits = Deposit::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $withdrawals = WithdrawalRequest::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('wallet.index', [
            'wallet' => $wallet,
            'activeSession' => $activeSession,
            'recentDeposits' => $recentDeposits,
            'withdrawals' => $withdrawals,
            'user' => $user,
        ]);
    }

    public function requestDepositAddress(): RedirectResponse
    {
        $user = Auth::user();

        // Reuse current session if still valid.
        $existing = DepositSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('reserved_until', '>', now())
            ->first();

        if ($existing) {
            return back()->with('status', 'Deposit address is active for 30 minutes.');
        }

        DB::transaction(function () use ($user): void {
            // Find a free address (no active reservation).
            $reservedAddressIds = DepositSession::query()
                ->where('status', 'active')
                ->where('reserved_until', '>', now())
                ->pluck('deposit_address_id')
                ->all();

            /** @var DepositAddress|null $address */
            $address = DepositAddress::query()
                ->where('is_active', true)
                ->whereNotIn('id', $reservedAddressIds)
                ->lockForUpdate()
                ->orderBy('last_assigned_at')
                ->orderBy('id')
                ->first();

            if (!$address) {
                abort(503, 'No deposit address available. Please try again later.');
            }

            $address->forceFill(['last_assigned_at' => now()])->save();

            DepositSession::create([
                'user_id' => $user->id,
                'deposit_address_id' => $address->id,
                'currency' => 'USDT',
                'network' => 'BEP20',
                'status' => 'active',
                'reserved_until' => now()->addMinutes(30),
            ]);
        });

        return back()->with('status', 'Deposit address generated. Valid for 30 minutes.');
    }

    public function updatePayoutAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payout_address' => ['nullable', 'string', 'regex:/^0x[a-fA-F0-9]{40}$/'],
        ]);

        $user = Auth::user();
        $user->forceFill(['payout_address' => $validated['payout_address'] ?? null])->save();

        return back()->with('status', 'Payout address updated.');
    }

    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'fee_type' => ['required', 'in:qos_15,qbit_10'],
        ]);

        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $amount = number_format((float) $validated['amount'], 2, '.', '');
        $feePct = $validated['fee_type'] === 'qos_15' ? '0.15' : '0.10';
        $feeAmount = bcmul($amount, $feePct, 2);
        $netAmount = bcsub($amount, $feeAmount, 2);

        if (!$user->payout_address) {
            return back()->with('status', 'Please set your payout address first.');
        }

        DB::transaction(function () use ($user, $wallet, $amount, $feeAmount, $netAmount, $validated): void {
            $wallet->refresh();
            if (bccomp((string) $wallet->balance, (string) $amount, 2) < 0) {
                abort(422, 'Insufficient wallet balance.');
            }

            // Debit immediately to reserve funds.
            $tx = $wallet->transactions()->create([
                'type' => 'withdrawal_request_debit',
                'amount' => bcmul($amount, '-1', 2),
                'meta' => [
                    'fee_type' => $validated['fee_type'],
                    'fee_amount' => $feeAmount,
                    'net_amount' => $netAmount,
                ],
                'occurred_on' => BusinessTime::today()->toDateString(),
            ]);

            $wallet->decrement('balance', $amount);

            WithdrawalRequest::create([
                'user_id' => $user->id,
                'currency' => 'USDT',
                'network' => 'BEP20',
                'to_address' => $user->payout_address,
                'amount' => $amount,
                'fee_type' => $validated['fee_type'],
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'wallet_transaction_id' => $tx->id,
                'requested_at' => now(),
            ]);
        });

        return back()->with('status', 'Withdrawal request submitted.');
    }
}
