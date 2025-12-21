<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Wallet') }}
        </h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-muted text-gray-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="surface">
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 surface-muted">
                                <div class="text-sm text-gray-600">{{ __('Registered Wallet (Deposits)') }}</div>
                                <div class="text-2xl font-semibold">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                        <div class="mt-1 text-xs text-gray-600">{{ __('Used to buy Quantum Machines.') }}</div>
                            </div>
                            <div class="p-4 surface-muted">
                                <div class="text-sm text-gray-600">{{ __('Quant Wallet (Earnings)') }}</div>
                                <div class="text-2xl font-semibold">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-gray-600">{{ __('Daily QOS + sponsor + network + node rewards.') }}</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="text-lg font-medium mb-2">{{ __('Deposit (USDT BEP20)') }}</div>

                            @if ($activeSession)
                                <div
                                    class="p-4 rounded-2xl bg-amber-500/10 ring-1 ring-amber-300/20"
                                    x-data="{
                                        expiresAt: @js($activeSession->reserved_until->timestamp),
                                        total: 1800,
                                        remaining: 0,
                                        timer: null,
                                        tick() {
                                            const now = Math.floor(Date.now() / 1000);
                                            this.remaining = Math.max(0, this.expiresAt - now);
                                        },
                                        start() {
                                            this.tick();
                                            this.timer = setInterval(() => this.tick(), 1000);
                                        },
                                        minutes() { return String(Math.floor(this.remaining / 60)).padStart(2, '0'); },
                                        seconds() { return String(this.remaining % 60).padStart(2, '0'); },
                                        pct() {
                                            const used = this.total - this.remaining;
                                            return Math.max(0, Math.min(100, Math.round((used / this.total) * 100)));
                                        },
                                    }"
                                    x-init="start()"
                                >
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <div class="text-sm text-slate-900">
                                                {{ __('Deposit address') }}
                                                <span class="text-slate-700">({{ __('valid for 30 minutes') }})</span>
                                            </div>
                                            <div class="mt-1 font-mono text-sm break-all">{{ $activeSession->depositAddress->address }}</div>
                                        </div>

                                        <div class="min-w-[220px]">
                                            <div class="text-xs text-slate-700">{{ __('Time remaining') }}</div>
                                            <div class="mt-1 flex items-center justify-between gap-3">
                                                <div class="text-lg font-semibold tabular-nums" x-text="minutes() + ':' + seconds()"></div>
                                                <div class="text-xs text-slate-700">
                                                    {{ __('Expires at') }} {{ $activeSession->reserved_until->format('H:i:s') }}
                                                </div>
                                            </div>
                                            <div class="mt-2 h-2 rounded-full bg-white/70 ring-1 ring-slate-900/10 overflow-hidden">
                                                <div class="h-2 bg-amber-400 rounded-full transition-[width] duration-500" :style="`width: ${pct()}%`"></div>
                                            </div>
                                            <div class="mt-2 text-xs text-amber-900" x-show="remaining <= 60" x-cloak>
                                                {{ __('Warning: less than 1 minute left. If it expires, request a new address.') }}
                                            </div>
                                            <div class="mt-2 text-xs text-slate-700" x-show="remaining === 0" x-cloak>
                                                {{ __('This address has expired. Please request a new deposit address.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-sm text-slate-800">
                                        {{ __('Please send USDT (BEP20) to this address before the timer ends.') }}
                                        {{ __('Deposits sent after expiry may not be credited to your account.') }}
                                    </div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('wallet.deposit.request') }}">
                                    @csrf
                                    <x-primary-button>
                                        {{ __('Get deposit address (30 min)') }}
                                    </x-primary-button>
                                </form>
                            @endif

                            <div class="mt-2 text-xs text-gray-600">
                                <div class="surface-muted p-3 text-gray-700">
                                    <div class="font-medium text-gray-900">{{ __('Deposit rules (USDT BEP20)') }}</div>
                                    <ul class="mt-1 space-y-1">
                                        <li>- {{ __('You must deposit within the 30-minute window shown above.') }}</li>
                                        <li>- {{ __('If it expires, request a new deposit address.') }}</li>
                                        <li>- {{ __('The system credits your Registered Wallet automatically once the transfer is detected.') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="surface">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium mb-2">{{ __('Withdrawal') }}</div>
                        <div class="mb-3 text-sm text-gray-600">
                            {{ __('Withdrawals are taken from your Quant Wallet.') }}
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
                                <select id="fee_type" name="fee_type" class="mt-1 select">
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
                <div class="surface">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium mb-3">{{ __('Recent deposits') }}</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-slate-900/5">
                                        <th class="py-2 pr-4">Time</th>
                                        <th class="py-2 pr-4">Amount</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2 pr-4">Tx</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentDeposits as $d)
                                        <tr class="border-b border-slate-900/5">
                                            <td class="py-2 pr-4">{{ $d->created_at }}</td>
                                            <td class="py-2 pr-4">{{ number_format((float) $d->amount, 2) }}</td>
                                            <td class="py-2 pr-4">{{ $d->status }}</td>
                                            <td class="py-2 pr-4 font-mono text-xs">{{ $d->tx_hash }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="py-2 text-gray-600" colspan="4">{{ __('No deposits yet.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="surface">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium mb-3">{{ __('Withdrawals') }}</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-slate-900/5">
                                        <th class="py-2 pr-4">Time</th>
                                        <th class="py-2 pr-4">Amount</th>
                                        <th class="py-2 pr-4">Net</th>
                                        <th class="py-2 pr-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($withdrawals as $w)
                                        <tr class="border-b border-slate-900/5">
                                            <td class="py-2 pr-4">{{ $w->requested_at }}</td>
                                            <td class="py-2 pr-4">{{ number_format((float) $w->amount, 2) }}</td>
                                            <td class="py-2 pr-4">{{ number_format((float) $w->net_amount, 2) }}</td>
                                            <td class="py-2 pr-4">{{ $w->status }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="py-2 text-gray-600" colspan="4">{{ __('No withdrawals yet.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</x-app-layout>
