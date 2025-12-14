<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPackage;
use App\Models\RoiRate;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvestmentPackageController extends Controller
{
    public function index(): View
    {
        $packages = InvestmentPackage::query()
            ->where('is_active', true)
            ->orderBy('amount')
            ->get();

        $today = Carbon::today();
        $todayRate = RoiRate::query()->whereDate('date', $today)->first();

        $user = Auth::user();
        $registeredWallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);
        $commissionWallet = Wallet::forUser($user->id, Wallet::TYPE_COMMISSION);

        return view('packages.index', [
            'user' => $user,
            'registeredWallet' => $registeredWallet,
            'commissionWallet' => $commissionWallet,
            'packages' => $packages,
            'today' => $today,
            'todayRate' => $todayRate,
        ]);
    }
}
