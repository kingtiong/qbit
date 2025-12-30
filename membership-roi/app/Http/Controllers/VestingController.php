<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class VestingController extends Controller
{
    public function index(): View
    {
        return view('vesting.index');
    }
}

