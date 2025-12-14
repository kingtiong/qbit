<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalsController extends Controller
{
    public function index(): View
    {
        $withdrawals = WithdrawalRequest::query()
            ->with(['user', 'processedBy'])
            ->orderByRaw("FIELD(status, 'pending','approved','paid','rejected')")
            ->orderByDesc('requested_at')
            ->paginate(50);

        return view('admin.withdrawals.index', [
            'withdrawals' => $withdrawals,
        ]);
    }

    public function approve(WithdrawalRequest $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('status', 'Withdrawal is not pending.');
        }

        $withdrawal->forceFill([
            'status' => 'approved',
            'processed_by_user_id' => Auth::id(),
            'processed_at' => now(),
        ])->save();

        return back()->with('status', 'Withdrawal approved.');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'pending' && $withdrawal->status !== 'approved') {
            return back()->with('status', 'Withdrawal cannot be rejected.');
        }

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($withdrawal, $request): void {
            // Refund the reserved amount back to wallet.
            $wallet = Wallet::forUser($withdrawal->user_id, Wallet::TYPE_COMMISSION);

            $refundTx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal_refund_credit',
                'amount' => $withdrawal->amount,
                'meta' => [
                    'withdrawal_request_id' => $withdrawal->id,
                    'reason' => $request->input('admin_note'),
                ],
                'occurred_on' => now()->toDateString(),
            ]);

            $wallet->increment('balance', $withdrawal->amount);

            $withdrawal->forceFill([
                'status' => 'rejected',
                'processed_by_user_id' => Auth::id(),
                'processed_at' => now(),
                'admin_note' => $request->input('admin_note'),
            ])->save();
        });

        return back()->with('status', 'Withdrawal rejected and refunded.');
    }

    public function markPaid(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'approved') {
            return back()->with('status', 'Withdrawal must be approved first.');
        }

        $validated = $request->validate([
            'tx_hash' => ['nullable', 'string', 'max:80'],
        ]);

        $withdrawal->forceFill([
            'status' => 'paid',
            'tx_hash' => $validated['tx_hash'] ?? null,
            'processed_by_user_id' => Auth::id(),
            'processed_at' => now(),
        ])->save();

        return back()->with('status', 'Withdrawal marked as paid.');
    }
}
