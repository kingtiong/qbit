<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentPackage;
use App\Models\SalesEvent;
use App\Models\Wallet;
use App\Services\BusinessTime;
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
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $investments = Investment::query()
            ->where('user_id', $user->id)
            ->with('package')
            ->orderByDesc('id')
            ->get();

        $recentTransactions = $wallet->transactions()
            ->orderByDesc('occurred_on')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('dashboard', [
            'wallet' => $wallet,
            'investments' => $investments,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investment_package_id' => ['required', 'integer', 'exists:investment_packages,id'],
        ]);

        $user = Auth::user();

        /** @var InvestmentPackage $package */
        $package = InvestmentPackage::query()
            ->whereKey($validated['investment_package_id'])
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($user, $package): void {
            $startedOn = BusinessTime::today()->toDateString();
            $maxReturnAmount = bcmul((string) $package->amount, (string) ($package->max_return_multiplier ?? '0'), 2);

            Investment::create([
                'user_id' => $user->id,
                'investment_package_id' => $package->id,
                'currency' => $package->currency,
                'amount' => $package->amount,
                'status' => 'active',
                'started_on' => $startedOn,
                'max_return_amount' => $maxReturnAmount,
            ]);

            SalesEvent::create([
                'user_id' => $user->id,
                'currency' => $package->currency,
                'amount' => $package->amount,
                'type' => 'investment_purchase',
                'occurred_on' => $startedOn,
                'meta' => [
                    'investment_package_id' => $package->id,
                    'package_code' => $package->code,
                ],
            ]);
        });

        return back()->with('status', 'Investment created.');
    }
}
