<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with(['registeredWallet', 'commissionWallet'])
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
}
