<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-100 leading-tight">
            {{ __('Wallet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-emerald-950 border border-emerald-800 text-emerald-100 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-6 text-slate-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded border border-white/10 bg-gray-950">
                                <div class="text-sm text-slate-300">Registered Wallet (Deposits)</div>
                                <div class="text-2xl font-semibold">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-slate-300">Used to buy Quantum Machines.</div>
                            </div>
                            <div class="p-4 rounded border border-white/10 bg-gray-950">
                                <div class="text-sm text-slate-300">Commission Wallet (Earnings)</div>
                                <div class="text-2xl font-semibold">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-slate-300">Daily QOS + sponsor + network + node rewards.</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="text-lg font-medium mb-2">Deposit (USDT BEP20)</div>

                            @if ($activeSession)
                                <div class="p-3 bg-emerald-950 border border-emerald-800 rounded">
                                    <div class="text-sm text-emerald-100">Deposit address (valid until {{ $activeSession->reserved_until->toDateTimeString() }})</div>
                                    <div class="mt-1 font-mono text-sm">{{ $activeSession->depositAddress->address }}</div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('wallet.deposit.request') }}">
                                    @csrf
                                    <x-primary-button>
                                        {{ __('Get deposit address (30 min)') }}
                                    </x-primary-button>
                                </form>
                            @endif

                            <div class="mt-2 text-xs text-slate-300">
                                After you send USDT to the address, the system will auto-credit your balance when detected.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-6 text-slate-100">
                        <div class="text-lg font-medium mb-2">Withdrawal</div>
                        <div class="mb-3 text-sm text-slate-300">
                            Withdrawals are taken from your <span class="font-medium">Commission Wallet</span>.
                        </div>

                        <form method="POST" action="{{ route('wallet.payout.update') }}" class="mb-4">
                            @csrf
                            @method('PUT')
                            <x-input-label for="payout_address" :value="__('Your BEP20 address')" />
                            <x-text-input id="payout_address" name="payout_address" type="text" class="mt-1 block w-full" :value="old('payout_address', $user->payout_address)" placeholder="0x..." />
                            <x-input-error class="mt-2" :messages="$errors->get('payout_address')" />
                            <div class="mt-2">
                                <x-primary-button>{{ __('Save address') }}</x-primary-button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('wallet.withdraw.request') }}" class="space-y-3">
                            @csrf
                            <div>
                                <x-input-label for="amount" :value="__('Amount (USDT)')" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('amount')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                            </div>

                            <div>
                                <x-input-label for="fee_type" :value="__('Withdrawal fee option')" />
                                <select id="fee_type" name="fee_type" class="mt-1 block w-full bg-gray-950 text-slate-100 border-white/10 rounded">
                                    <option value="qos_15">Deduct QOS 15%</option>
                                    <option value="qbit_10">Deduct QBIT 10%</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('fee_type')" />
                            </div>

                            <x-primary-button>{{ __('Request withdrawal') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-6 text-slate-100">
                        <div class="text-lg font-medium mb-3">Recent deposits</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-white/10">
                                        <th class="py-2 pr-4">Time</th>
                                        <th class="py-2 pr-4">Amount</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2 pr-4">Tx</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentDeposits as $d)
                                        <tr class="border-b border-white/10">
                                            <td class="py-2 pr-4">{{ $d->created_at }}</td>
                                            <td class="py-2 pr-4">{{ number_format((float) $d->amount, 2) }}</td>
                                            <td class="py-2 pr-4">{{ $d->status }}</td>
                                            <td class="py-2 pr-4 font-mono text-xs">{{ $d->tx_hash }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="py-2 text-slate-300" colspan="4">No deposits yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-6 text-slate-100">
                        <div class="text-lg font-medium mb-3">Withdrawals</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-white/10">
                                        <th class="py-2 pr-4">Time</th>
                                        <th class="py-2 pr-4">Amount</th>
                                        <th class="py-2 pr-4">Net</th>
                                        <th class="py-2 pr-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($withdrawals as $w)
                                        <tr class="border-b border-white/10">
                                            <td class="py-2 pr-4">{{ $w->requested_at }}</td>
                                            <td class="py-2 pr-4">{{ number_format((float) $w->amount, 2) }}</td>
                                            <td class="py-2 pr-4">{{ number_format((float) $w->net_amount, 2) }}</td>
                                            <td class="py-2 pr-4">{{ $w->status }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="py-2 text-slate-300" colspan="4">No withdrawals yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
