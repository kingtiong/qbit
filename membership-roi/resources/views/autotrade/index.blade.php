<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Auto Trade') }}</h2>
                <div class="text-sm text-gray-600">{{ __('Top 20 market simulation with live ticks and trade history.') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="btn-neutral normal-case text-sm">{{ __('Dashboard') }}</a>
                <a href="{{ route('wallet.index') }}" class="btn-neutral normal-case text-sm">{{ __('Wallet') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="surface p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Fund size') }}</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">USDT {{ number_format((float) $fund, 2) }}</div>
                <div class="mt-1 text-xs text-gray-600">{{ __('Configured by admin') }}</div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Target') }}</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format((float) $targetPct, 2) }}%</div>
                <div class="mt-1 text-xs text-gray-600">{{ __('Basic target profit per day') }}</div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Today P&L') }}</div>
                <div class="mt-1 text-2xl font-semibold {{ $todayPnl < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                    USDT {{ number_format((float) $todayPnl, 2) }}
                </div>
                <div class="mt-1 text-xs text-gray-600">
                    {{ __('Target') }}: USDT {{ number_format((float) $targetPnl, 2) }}
                </div>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-700">
            <span class="font-medium">{{ __('Note') }}:</span>
            {{ __('This is a simulated display for user experience only; it does not place real exchange orders.') }}
        </div>
    </div>

    <div
        class="surface p-6 mb-6"
        x-data="{
            symbols: @js($symbols),
            state: {},
            init() {
                // Initialize mock prices / change and update every second
                this.symbols.forEach((s, i) => {
                    const base = 10 + (i * 3);
                    this.state[s] = {
                        price: base,
                        chg: (Math.random() - 0.5) * 2,
                        pulse: false,
                    };
                });

                setInterval(() => {
                    this.symbols.forEach((s) => {
                        const x = this.state[s];
                        const delta = (Math.random() - 0.5) * 0.8;
                        x.price = Math.max(0.0001, x.price + delta);
                        x.chg = Math.max(-9.99, Math.min(9.99, x.chg + (Math.random() - 0.5) * 0.3));
                        x.pulse = true;
                        setTimeout(() => (x.pulse = false), 150);
                    });
                }, 1000);
            },
            fmtPrice(v) {
                const n = Number(v);
                if (n >= 1000) return n.toFixed(0);
                if (n >= 10) return n.toFixed(2);
                return n.toFixed(4);
            },
            fmtPct(v) {
                const n = Number(v);
                return (n >= 0 ? '+' : '') + n.toFixed(2) + '%';
            }
        }"
        x-init="init()"
    >
        <div class="flex items-end justify-between gap-3 mb-4">
            <div>
                <div class="text-lg font-medium text-gray-900">{{ __('Top 20 Crypto (Live)') }}</div>
                <div class="text-sm text-gray-600">{{ __('Animated ticks update every second.') }}</div>
            </div>
            <div class="text-xs text-gray-600">
                {{ __('Pairs') }}: USDT
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <template x-for="s in symbols" :key="s">
                <div class="surface-muted p-3">
                    <div class="flex items-center justify-between">
                        <div class="font-semibold text-gray-900" x-text="s"></div>
                        <div
                            class="text-xs"
                            :class="state[s].chg >= 0 ? 'text-emerald-700' : 'text-red-600'"
                            x-text="fmtPct(state[s].chg)"
                        ></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-sm text-gray-600" x-text="s + '/USDT'"></div>
                        <div
                            class="text-sm font-semibold tabular-nums"
                            :class="state[s].pulse ? 'text-gray-900' : 'text-gray-900'"
                            x-text="'$' + fmtPrice(state[s].price)"
                        ></div>
                    </div>
                    <div class="mt-2 h-1.5 rounded-full bg-white ring-1 ring-slate-900/10 overflow-hidden">
                        <div class="h-1.5 bg-emerald-600/60 rounded-full" :style="`width: ${50 + state[s].chg * 4}%`"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="surface p-6">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
            <div>
                <div class="text-lg font-medium text-gray-900">{{ __('Trade history') }}</div>
                <div class="text-sm text-gray-600">{{ __('Shows simulated buy/sell prices and P&L per trade.') }}</div>
            </div>
            <div class="text-xs text-gray-600">
                {{ __('Latest') }}: {{ $today->toDateString() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-900/5">
                        <th class="py-2 pr-4">{{ __('Date') }}</th>
                        <th class="py-2 pr-4">{{ __('Pair') }}</th>
                        <th class="py-2 pr-4">{{ __('Qty') }}</th>
                        <th class="py-2 pr-4">{{ __('Buy') }}</th>
                        <th class="py-2 pr-4">{{ __('Sell') }}</th>
                        <th class="py-2 pr-4">{{ __('P&L') }}</th>
                        <th class="py-2 pr-4">{{ __('P&L %') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trades as $t)
                        @php
                            $pair = ($t->symbol ?? '').'/'.($t->pair ?? 'USDT');
                            $pnl = (float) $t->pnl;
                        @endphp
                        <tr class="border-b border-slate-900/5">
                            <td class="py-2 pr-4">{{ $t->trade_date?->toDateString() }}</td>
                            <td class="py-2 pr-4 font-medium">{{ $pair }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ number_format((float) $t->qty, 6) }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ number_format((float) $t->buy_price, 6) }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ number_format((float) $t->sell_price, 6) }}</td>
                            <td class="py-2 pr-4 font-semibold {{ $pnl < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ number_format($pnl, 2) }}
                            </td>
                            <td class="py-2 pr-4 tabular-nums {{ (float) $t->pnl_pct < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ number_format((float) $t->pnl_pct, 2) }}%
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-2 text-gray-600" colspan="7">{{ __('No trades yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

