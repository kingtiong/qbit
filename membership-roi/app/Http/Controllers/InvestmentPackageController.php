<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPackage;
use App\Models\RoiRate;
use Carbon\Carbon;
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

        return view('packages.index', [
            'packages' => $packages,
            'today' => $today,
            'todayRate' => $todayRate,
        ]);
    }
}
