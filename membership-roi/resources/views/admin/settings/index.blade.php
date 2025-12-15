<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Settings</h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-muted text-gray-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="surface">
        <div class="p-6 text-gray-900">
                    <div class="text-lg font-medium mb-3">Deposit address pool (USDT BEP20)</div>

                    <form method="POST" action="{{ route('admin.settings.deposit_addresses.add') }}" class="flex gap-2 mb-4">
                        @csrf
                        <div class="w-full">
                            <input name="address" placeholder="0x..." value="{{ old('address') }}" class="input w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                        <button class="btn-dark px-4 py-2 normal-case">Add</button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-900/5">
                                    <th class="py-2 pr-4">ID</th>
                                    <th class="py-2 pr-4">Address</th>
                                    <th class="py-2 pr-4">Active</th>
                                    <th class="py-2 pr-4">Last assigned</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($addresses as $a)
                                    <tr class="border-b border-slate-900/5">
                                        <td class="py-2 pr-4">{{ $a->id }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs">{{ $a->address }}</td>
                                        <td class="py-2 pr-4">{{ $a->is_active ? 'yes' : 'no' }}</td>
                                        <td class="py-2 pr-4">{{ $a->last_assigned_at }}</td>
                                        <td class="py-2 pr-4">
                                            <form method="POST" action="{{ route('admin.settings.deposit_addresses.toggle', $a) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded-xl text-xs font-semibold {{ $a->is_active ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white' }}">
                                                    {{ $a->is_active ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>

    <div class="surface mt-6">
        <div class="p-6 text-gray-900">
            <div class="text-lg font-medium mb-3">Auto Trade Settings</div>
            <div class="text-sm text-gray-600 mb-4">
                Configure the simulated auto trading dashboard fund size and target daily profit %.
            </div>

            <form method="POST" action="{{ route('admin.settings.autotrade.update') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf

                <div>
                    <label class="text-sm text-gray-700">Fund size (USDT)</label>
                    <input name="fund_usdt" type="number" step="0.01" min="0" value="{{ old('fund_usdt', $autoTradeFund ?? 0) }}" class="input w-full mt-1" />
                    <x-input-error class="mt-2" :messages="$errors->get('fund_usdt')" />
                </div>

                <div>
                    <label class="text-sm text-gray-700">Target daily profit (%)</label>
                    <input name="daily_profit_pct" type="number" step="0.01" min="0" max="10" value="{{ old('daily_profit_pct', $autoTradeDailyPct ?? 1.5) }}" class="input w-full mt-1" />
                    <x-input-error class="mt-2" :messages="$errors->get('daily_profit_pct')" />
                </div>

                <div class="flex items-end">
                    <button class="btn-dark w-full normal-case">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
