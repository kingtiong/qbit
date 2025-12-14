<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\View\View;

class InvestmentsAdminController extends Controller
{
    public function index(): View
    {
        $investments = Investment::query()
            ->with(['user', 'package'])
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.investments.index', [
            'investments' => $investments,
        ]);
    }
}
