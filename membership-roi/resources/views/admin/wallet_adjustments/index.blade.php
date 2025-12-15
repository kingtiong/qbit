<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Wallet Adjustments</h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-muted text-gray-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="surface p-6 mb-6">
        <div class="text-lg font-medium text-gray-900 mb-3">Adjust user wallet</div>

        <form method="POST" action="{{ route('admin.wallet_adjustments.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf

            <div class="md:col-span-2">
                <label class="text-sm text-gray-700">User email</label>
                <input name="email" value="{{ old('email') }}" class="input w-full mt-1" placeholder="user@example.com" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <label class="text-sm text-gray-700">Wallet</label>
                <select name="wallet_type" class="select w-full mt-1">
                    <option value="registered" @selected(old('wallet_type') === 'registered')>Registered Wallet</option>
                    <option value="commission" @selected(old('wallet_type') === 'commission')>Quant Wallet</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('wallet_type')" />
            </div>

            <div>
                <label class="text-sm text-gray-700">Operation</label>
                <select name="operation" class="select w-full mt-1">
                    <option value="credit" @selected(old('operation') === 'credit')>Credit (+)</option>
                    <option value="debit" @selected(old('operation') === 'debit')>Debit (-)</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('operation')" />
            </div>

            <div>
                <label class="text-sm text-gray-700">Amount (USDT)</label>
                <input name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" class="input w-full mt-1" />
                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
            </div>

            <div class="md:col-span-3">
                <label class="text-sm text-gray-700">Note (optional)</label>
                <input name="note" value="{{ old('note') }}" class="input w-full mt-1" placeholder="Reason / reference" />
                <x-input-error class="mt-2" :messages="$errors->get('note')" />
            </div>

            <div class="flex items-end">
                <button class="btn-dark w-full normal-case">Apply</button>
            </div>
        </form>
    </div>

    <div class="surface p-6">
        <div class="text-lg font-medium text-gray-900 mb-3">Recent adjustments</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-900/5">
                        <th class="py-2 pr-4">Date</th>
                        <th class="py-2 pr-4">Type</th>
                        <th class="py-2 pr-4">Amount</th>
                        <th class="py-2 pr-4">Wallet ID</th>
                        <th class="py-2 pr-4">Meta</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentAdjustments as $tx)
                        <tr class="border-b border-slate-900/5">
                            <td class="py-2 pr-4">{{ $tx->occurred_on }}</td>
                            <td class="py-2 pr-4">{{ $tx->type }}</td>
                            <td class="py-2 pr-4 font-medium {{ (float) $tx->amount < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ number_format((float) $tx->amount, 2) }}
                            </td>
                            <td class="py-2 pr-4">{{ $tx->wallet_id }}</td>
                            <td class="py-2 pr-4">
                                <div class="font-mono text-xs text-slate-700 break-all">
                                    {{ json_encode($tx->meta) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-2 text-gray-600" colspan="5">No adjustments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>

