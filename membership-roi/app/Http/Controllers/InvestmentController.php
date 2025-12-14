<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentPackage;
use App\Models\RoiEarning;
use App\Models\Wallet;
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

        $recentEarnings = RoiEarning::query()
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(20)
            ->get();

        return view('dashboard', [
            'wallet' => $wallet,
            'investments' => $investments,
            'recentEarnings' => $recentEarnings,
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
            Investment::create([
                'user_id' => $user->id,
                'investment_package_id' => $package->id,
                'currency' => $package->currency,
                'amount' => $package->amount,
                'status' => 'active',
                'started_on' => Carbon::today()->toDateString(),
            ]);
        });

        return back()->with('status', 'Investment created.');
    }
}
