<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NetworkController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $referrals = $user->referrals()
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('network.index', [
            'user' => $user,
            'referrals' => $referrals,
            'inviteLink' => url('/invite/'.$user->invite_code),
        ]);
    }
}

