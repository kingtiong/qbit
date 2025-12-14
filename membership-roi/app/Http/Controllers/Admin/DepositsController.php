<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\View\View;

class DepositsController extends Controller
{
    public function index(): View
    {
        $deposits = Deposit::query()
            ->with(['user'])
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.deposits.index', [
            'deposits' => $deposits,
        ]);
    }
}
