<?php

namespace App\Http\Controllers;

use App\Models\PartnershipPackage;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QbpController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $registeredWallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);
        $commissionWallet = Wallet::forUser($user->id, Wallet::TYPE_COMMISSION);

        $qbpPackages = PartnershipPackage::query()
            ->where('is_active', true)
            ->orderBy('level')
            ->get();

        return view('qbp.index', [
            'user' => $user,
            'registeredWallet' => $registeredWallet,
            'commissionWallet' => $commissionWallet,
            'qbpPackages' => $qbpPackages,
        ]);
    }
}

