<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\BusinessTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletAdjustmentsController extends Controller
{
    public function index(Request $request): View
    {
        $recentAdjustments = WalletTransaction::query()
            ->whereIn('type', ['admin_adjustment_credit', 'admin_adjustment_debit'])
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return view('admin.wallet_adjustments.index', [
            'recentAdjustments' => $recentAdjustments,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'wallet_type' => ['required', 'in:registered,commission'],
            'operation' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var User|null $user */
        $user = User::query()->where('email', $validated['email'])->first();
        if (!$user) {
            return back()->with('status', 'User not found.');
        }

        $walletType = $validated['wallet_type'];
        $operation = $validated['operation'];
        $amount = number_format((float) $validated['amount'], 2, '.', '');

        DB::transaction(function () use ($user, $walletType, $operation, $amount, $validated): void {
            $wallet = Wallet::forUser($user->id, $walletType);

            /** @var Wallet $locked */
            $locked = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            if ($operation === 'debit') {
                if (bccomp((string) $locked->balance, (string) $amount, 2) < 0) {
                    abort(422, 'Insufficient balance for debit.');
                }
            }

            $signed = $operation === 'debit' ? bcmul($amount, '-1', 2) : $amount;
            $txType = $operation === 'debit' ? 'admin_adjustment_debit' : 'admin_adjustment_credit';

            $locked->transactions()->create([
                'type' => $txType,
                'amount' => $signed,
                'meta' => [
                    'note' => $validated['note'] ?? null,
                    'target_user_id' => $user->id,
                    'target_email' => $user->email,
                    'wallet_type' => $walletType,
                    'admin_user_id' => Auth::guard('admin')->id(),
                    'admin_email' => Auth::guard('admin')->user()?->email,
                ],
                'occurred_on' => BusinessTime::today()->toDateString(),
            ]);

            if ($operation === 'debit') {
                $locked->decrement('balance', $amount);
            } else {
                $locked->increment('balance', $amount);
            }
        });

        return back()->with('status', 'Wallet adjusted successfully.');
    }
}

