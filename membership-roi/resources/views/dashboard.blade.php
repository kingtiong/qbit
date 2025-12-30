<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quantum Trading Dashboard') }}</h2>
                <div class="text-sm text-gray-600">
                    {{ __('Welcome back') }}, <span class="font-medium">{{ $user->name }}</span>
                    @if ($user->rank)
                        • {{ __('Rank') }}: <span class="font-medium">{{ $user->rank }}</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('wallet.index') }}" class="btn-dark normal-case text-sm">
                    {{ __('Wallet') }}
                </a>
                <a href="{{ route('packages.index') }}" class="btn-primary normal-case text-sm">
                    {{ __('Activate Quantum Machine') }}
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-gold">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6 surface overflow-hidden">
        <div class="p-6 surface-gold">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm text-slate-700">{{ __('Quantum Trading • Automated strategy execution') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ __('Your machines trade your fund daily') }}</div>
                            <div class="mt-2 text-sm text-slate-700 max-w-3xl">
                                {{ __('Deposit into your Registered Wallet, activate a Quantum Machine (QPU), and receive daily QOS earnings + network rewards into your Quant Wallet.') }}
                            </div>
                            <div
                                class="mt-4 text-sm text-slate-700"
                                x-data="{
                                    link: @js(url('/invite/'.$user->invite_code)),
                                    copied: false,
                                    async copy() {
                                        try {
                                            if (navigator?.clipboard?.writeText) {
                                                await navigator.clipboard.writeText(this.link);
                                            } else {
                                                const ta = document.createElement('textarea');
                                                ta.value = this.link;
                                                ta.style.position = 'fixed';
                                                ta.style.left = '-9999px';
                                                document.body.appendChild(ta);
                                                ta.select();
                                                document.execCommand('copy');
                                                document.body.removeChild(ta);
                                            }
                                            this.copied = true;
                                            setTimeout(() => (this.copied = false), 1500);
                                        } catch (e) {
                                            // ignore
                                        }
                                    },
                                }"
                            >
                                <div class="sm:inline">{{ __('Invitation link') }}:</div>
                                <div class="sm:ml-2 mt-2 sm:mt-0 inline-flex flex-wrap items-center gap-2 align-middle">
                                    <span class="inline-block font-mono text-xs bg-black/60 text-amber-50 px-3 py-2 rounded-xl ring-1 ring-amber-300/25 break-all">
                                        {{ url('/invite/'.$user->invite_code) }}
                                    </span>
                                    <button type="button" class="btn-primary normal-case text-xs px-3 py-2" @click="copy()">
                                        <span x-show="!copied">{{ __('Copy') }}</span>
                                        <span x-show="copied" x-cloak>{{ __('Copied') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('wallet.index') }}" class="btn-neutral normal-case text-sm">
                                {{ __('Deposit') }}
                            </a>
                            <a href="{{ route('wallet.index') }}" class="btn-neutral normal-case text-sm">
                                {{ __('Withdraw') }}
                            </a>
                            <a href="{{ route('packages.index') }}" class="btn-primary normal-case text-sm">
                                {{ __('Activate Machine') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="surface">
                    <div class="p-5">
                        <div class="text-sm text-gray-600">{{ __('Registered Wallet (Deposits)') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                        <div class="mt-2 text-sm text-gray-600">{{ __('Used to activate Quantum Machines.') }}</div>
                    </div>
                </div>
                <div class="surface">
                    <div class="p-5">
                        <div class="text-sm text-gray-600">{{ __('Quant Wallet (Earnings)') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                        <div class="mt-2 text-sm text-gray-600">{{ __('Daily QOS + network + node rewards.') }}</div>
                    </div>
                </div>
                <div class="surface">
                    <div class="p-5">
                        <div class="text-sm text-gray-600">{{ __('xQBT') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">{{ number_format((float) ($xqbtUnits ?? 0), 0) }}</div>
                        <div class="mt-2 text-sm text-gray-600">{{ __('Genesis Node Network units') }}</div>
                    </div>
                </div>
                <div class="surface">
                    <div class="p-5">
                        <div class="text-sm text-gray-600">{{ __('QBT Wallet') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">USDT {{ number_format((float) ($qbtWalletValue ?? 0), 2) }}</div>
                        <div class="mt-2 text-sm text-gray-600">{{ __('Unit price') }}: USDT {{ number_format((float) ($qbtUnitPrice ?? 0), 0) }}</div>
                    </div>
                </div>
                <div class="surface">
                    <div class="p-5">
                        <div class="text-sm text-gray-600">{{ __('Machines') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ (int) ($summary['active_count'] ?? 0) }} {{ __('active') }}</div>
                        <div class="mt-2 text-sm text-gray-600">
                            {{ __('Total earned') }}: USDT {{ number_format((float) ($summary['total_earned'] ?? 0), 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="surface">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
                            <div>
                                <div class="text-lg font-medium">{{ __('My Quantum Machines') }}</div>
                                <div class="text-sm text-gray-600">{{ __('Each machine has its own max return cap and progress.') }}</div>
                            </div>
                            <a href="{{ route('packages.index') }}" class="text-sm font-medium text-amber-200 hover:underline">{{ __('View Machines') }}</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-slate-900/5">
                                        <th class="py-2 pr-4">{{ __('Machine') }}</th>
                                        <th class="py-2 pr-4">{{ __('Capital') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Earned') }}</th>
                                        <th class="py-2 pr-4">{{ __('Progress') }}</th>
                                        <th class="py-2 pr-4">{{ __('Max Return') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($investments as $inv)
                                        <tr class="border-b border-slate-900/5">
                                            <td class="py-3 pr-4">
                                                <div class="font-medium">{{ $inv->package?->label ?? ('QPU #'.$inv->investment_package_id) }}</div>
                                                <div class="text-xs text-gray-600">
                                                    @if ($inv->package?->daily_qos_amount)
                                                        {{ __('Daily QOS') }}: {{ $inv->currency }} {{ number_format((float) $inv->package->daily_qos_amount, 2) }}
                                                    @else
                                                        {{ __('Started') }}: {{ $inv->started_on?->toDateString() ?? '—' }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 pr-4">{{ $inv->currency }} {{ number_format((float) $inv->amount, 2) }}</td>
                                            <td class="py-3 pr-4">{{ $inv->status }}</td>
                                            <td class="py-3 pr-4">{{ $inv->currency }} {{ number_format((float) $inv->total_earned, 2) }}</td>
                                            <td class="py-3 pr-4">
                                                @php
                                                    $max = (float) ($inv->max_return_amount ?? 0);
                                                    $earned = (float) ($inv->total_earned ?? 0);
                                                    $pct = $max > 0 ? min(100, round(($earned / $max) * 100, 2)) : 0;
                                                @endphp
                                                <div class="text-xs text-gray-600">{{ $pct }}%</div>
                                                <div class="w-32 bg-slate-900/5 rounded-full h-2 mt-1">
                                                    <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                                </div>
                                            </td>
                                            <td class="py-3 pr-4">{{ $inv->currency }} {{ number_format((float) ($inv->max_return_amount ?? 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="py-3 text-gray-600" colspan="6">
                                                {{ __('You don’t have any machines yet. Deposit and activate your first Quantum Machine to start earning daily QOS.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="surface">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
                            <div>
                                <div class="text-lg font-medium">{{ __('Recent Activity') }}</div>
                                <div class="text-sm text-gray-600">{{ __('Deposits, earnings, purchases, and withdrawals.') }}</div>
                            </div>
                            <a href="{{ route('wallet.index') }}" class="text-sm font-medium text-amber-200 hover:underline">{{ __('Open Wallet') }}</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-slate-900/5">
                                        <th class="py-2 pr-4">{{ __('Date') }}</th>
                                        <th class="py-2 pr-4">{{ __('Wallet') }}</th>
                                        <th class="py-2 pr-4">{{ __('Type') }}</th>
                                        <th class="py-2 pr-4">{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $t)
                                        <tr class="border-b border-slate-900/5">
                                            <td class="py-3 pr-4">{{ $t->occurred_on->toDateString() }}</td>
                                            <td class="py-3 pr-4">
                                                @php $wt = $t->wallet?->type; @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs border {{ $wt === 'commission' ? 'bg-amber-500/10 border-amber-300/30 text-amber-200' : 'bg-white/5 border-white/10 text-amber-50/80' }}">
                                                    {{ $wt === 'commission' ? __('Quant') : __('Registered') }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4">{{ $t->type }}</td>
                                            <td class="py-3 pr-4 font-medium text-amber-50">
                                                {{ number_format((float) $t->amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="py-3 text-gray-600" colspan="4">{{ __('No activity yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 p-4 surface-muted">
                            <div class="font-medium text-gray-900">{{ __('How Quantum Trading works') }}</div>
                            <ul class="mt-2 text-sm text-gray-700 space-y-1">
                                <li><span class="font-medium">1)</span> {{ __('Deposit USDT (BEP20) → credited to your Registered Wallet.') }}</li>
                                <li><span class="font-medium">2)</span> {{ __('Activate a QPU Machine → your capital is allocated into the strategy engine.') }}</li>
                                <li><span class="font-medium">3)</span> {{ __('Earn daily QOS → credited into your Quant Wallet (UTC+8 daily distribution).') }}</li>
                                <li><span class="font-medium">4)</span> {{ __('Build a network → direct sponsor + rank bonus + leader node rewards (if eligible).') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
