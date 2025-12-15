<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $addresses = DepositAddress::query()
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        return view('admin.settings.index', [
            'addresses' => $addresses,
        ]);
    }

    public function addDepositAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{40}$/', 'unique:deposit_addresses,address'],
        ]);

        $address = strtolower(trim((string) $validated['address']));

        DepositAddress::create([
            'chain' => 'bsc',
            'address' => $address,
            'is_active' => true,
        ]);

        return back()->with('status', 'Deposit address added.');
    }

    public function toggleDepositAddress(DepositAddress $depositAddress): RedirectResponse
    {
        $depositAddress->forceFill(['is_active' => !$depositAddress->is_active])->save();
        return back()->with('status', 'Deposit address updated.');
    }
}
