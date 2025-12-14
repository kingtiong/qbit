<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-slate-100 leading-tight">Quantum Trading Dashboard</h2>
                <div class="text-sm text-slate-300">
                    Welcome back, <span class="font-medium">{{ $user->name }}</span>
                    @if ($user->rank)
                        • Rank: <span class="font-medium">{{ $user->rank }}</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 border border-white/10 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/15">
                    Wallet
                </a>
                <a href="{{ route('packages.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                    Buy Quantum Machine
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-emerald-950 border border-emerald-800 text-emerald-100 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-6 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white bg-gradient-to-r from-gray-900 via-emerald-900 to-gray-900">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm text-emerald-100">Quantum Trading • Automated strategy execution</div>
                            <div class="mt-1 text-2xl font-semibold">Your machines trade your fund daily</div>
                            <div class="mt-2 text-sm text-emerald-100 max-w-3xl">
                                Deposit into your <span class="font-medium">Registered Wallet</span>, buy a Quantum Machine (QPU),
                                and receive daily QOS earnings + network rewards into your <span class="font-medium">Commission Wallet</span>.
                            </div>
                            <div class="mt-4 text-sm text-emerald-100">
                                Invitation link:
                                <span class="ml-2 font-mono text-xs bg-white/10 px-2 py-1 rounded">{{ url('/invite/'.$user->invite_code) }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 border border-white/20 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/20">
                                Deposit
                            </a>
                            <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 border border-white/20 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/20">
                                Withdraw
                            </a>
                            <a href="{{ route('packages.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                                Buy Machine
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-5">
                        <div class="text-xs text-slate-300 uppercase tracking-wider">Registered Wallet</div>
                        <div class="mt-1 text-2xl font-semibold text-white">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                        <div class="mt-2 text-sm text-slate-300">Deposit funds used to buy machines.</div>
                    </div>
                </div>
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-5">
                        <div class="text-xs text-slate-300 uppercase tracking-wider">Commission Wallet</div>
                        <div class="mt-1 text-2xl font-semibold text-white">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                        <div class="mt-2 text-sm text-slate-300">Daily QOS + network + node rewards.</div>
                    </div>
                </div>
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-5">
                        <div class="text-xs text-slate-300 uppercase tracking-wider">Active Machines</div>
                        <div class="mt-1 text-2xl font-semibold text-white">{{ (int) ($summary['active_count'] ?? 0) }}</div>
                        <div class="mt-2 text-sm text-slate-300">Machines currently generating results.</div>
                    </div>
                </div>
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-5">
                        <div class="text-xs text-slate-300 uppercase tracking-wider">Total Earned (All Machines)</div>
                        <div class="mt-1 text-2xl font-semibold text-white">USDT {{ number_format((float) ($summary['total_earned'] ?? 0), 2) }}</div>
                        <div class="mt-2 text-sm text-slate-300">
                            Remaining to max: USDT {{ number_format((float) ($summary['total_remaining'] ?? 0), 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-6 text-slate-100">
                        <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
                            <div>
                                <div class="text-lg font-medium">My Quantum Machines</div>
                                <div class="text-sm text-slate-300">Each machine has its own max return cap and progress.</div>
                            </div>
                            <a href="{{ route('packages.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">View Machines</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-white/10">
                                        <th class="py-2 pr-4">Machine</th>
                                        <th class="py-2 pr-4">Capital</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2 pr-4">Earned</th>
                                        <th class="py-2 pr-4">Progress</th>
                                        <th class="py-2 pr-4">Max Return</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($investments as $inv)
                                        <tr class="border-b border-white/10">
                                            <td class="py-3 pr-4">
                                                <div class="font-medium">{{ $inv->package?->label ?? ('QPU #'.$inv->investment_package_id) }}</div>
                                                <div class="text-xs text-slate-300">
                                                    @if ($inv->package?->daily_qos_amount)
                                                        Daily QOS: {{ $inv->currency }} {{ number_format((float) $inv->package->daily_qos_amount, 2) }}
                                                    @else
                                                        Started: {{ $inv->started_on?->toDateString() ?? '—' }}
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
                                                <div class="text-xs text-slate-300">{{ $pct }}%</div>
                                                <div class="w-32 bg-white/10 rounded h-2 mt-1">
                                                    <div class="bg-emerald-500 h-2 rounded" style="width: {{ $pct }}%"></div>
                                                </div>
                                            </td>
                                            <td class="py-3 pr-4">{{ $inv->currency }} {{ number_format((float) ($inv->max_return_amount ?? 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="py-3 text-slate-300" colspan="6">
                                                You don’t have any machines yet. Deposit and buy your first Quantum Machine to start earning daily QOS.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
                    <div class="p-6 text-slate-100">
                        <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
                            <div>
                                <div class="text-lg font-medium">Recent Activity</div>
                                <div class="text-sm text-slate-300">Deposits, earnings, purchases, and withdrawals.</div>
                            </div>
                            <a href="{{ route('wallet.index') }}" class="text-sm font-medium text-emerald-300 hover:underline">Open Wallet</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-white/10">
                                        <th class="py-2 pr-4">Date</th>
                                        <th class="py-2 pr-4">Wallet</th>
                                        <th class="py-2 pr-4">Type</th>
                                        <th class="py-2 pr-4">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $t)
                                        <tr class="border-b border-white/10">
                                            <td class="py-3 pr-4">{{ $t->occurred_on->toDateString() }}</td>
                                            <td class="py-3 pr-4">
                                                @php $wt = $t->wallet?->type; @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs border {{ $wt === 'commission' ? 'bg-emerald-950 border-emerald-800 text-emerald-100' : 'bg-gray-950 border-white/10 text-slate-200' }}">
                                                    {{ $wt === 'commission' ? 'Commission' : 'Registered' }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4">{{ $t->type }}</td>
                                            <td class="py-3 pr-4 font-medium {{ (float) $t->amount < 0 ? 'text-red-300' : 'text-emerald-300' }}">
                                                {{ number_format((float) $t->amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="py-3 text-slate-300" colspan="4">No activity yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 p-4 rounded border border-white/10 bg-gray-950">
                            <div class="font-medium text-slate-100">How Quantum Trading works</div>
                            <ul class="mt-2 text-sm text-slate-200 space-y-1">
                                <li><span class="font-medium">1)</span> Deposit USDT (BEP20) → credited to your <span class="font-medium">Registered Wallet</span>.</li>
                                <li><span class="font-medium">2)</span> Buy a QPU Machine → your capital is allocated into the strategy engine.</li>
                                <li><span class="font-medium">3)</span> Earn daily QOS → credited into your <span class="font-medium">Commission Wallet</span> (UTC+8 daily distribution).</li>
                                <li><span class="font-medium">4)</span> Build a network → direct sponsor + rank bonus + leader node rewards (if eligible).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
