<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositAddress;
use App\Models\Setting;
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
            'autoTradeFund' => (float) Setting::getValue('autotrade.fund_usdt', '1000'),
            'autoTradeDailyPct' => (float) Setting::getValue('autotrade.daily_profit_pct', '1.5'),
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

    public function updateAutoTrade(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fund_usdt' => ['required', 'numeric', 'min:0'],
            'daily_profit_pct' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);

        Setting::putValue('autotrade.fund_usdt', number_format((float) $validated['fund_usdt'], 2, '.', ''));
        Setting::putValue('autotrade.daily_profit_pct', number_format((float) $validated['daily_profit_pct'], 2, '.', ''));

        return back()->with('status', 'Auto trade settings saved.');
    }
}
