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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Win rate') }}</div>
                <div class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">
                    {{ number_format((float) ($analytics['win_rate'] ?? 0), 2) }}%
                </div>
                <div class="mt-1 text-xs text-gray-600">
                    {{ (int) ($analytics['wins'] ?? 0) }}W / {{ (int) ($analytics['losses'] ?? 0) }}L
                </div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Long / Short') }}</div>
                <div class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">
                    {{ (int) ($analytics['longs'] ?? 0) }} / {{ (int) ($analytics['shorts'] ?? 0) }}
                </div>
                <div class="mt-1 text-xs text-gray-600">{{ __('Based on last trades') }}</div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Avg hold') }}</div>
                <div class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">
                    {{ number_format((float) ($analytics['avg_hold_min'] ?? 0), 1) }}m
                </div>
                <div class="mt-1 text-xs text-gray-600">{{ __('Average position duration') }}</div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Trades') }}</div>
                <div class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">
                    {{ (int) ($analytics['total'] ?? 0) }}
                </div>
                <div class="mt-1 text-xs text-gray-600">{{ __('Last 300 records') }}</div>
            </div>
        </div>

        <div class="surface-muted p-4 mt-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-gray-900">{{ __('P&L (last 30 days)') }}</div>
                    <div class="text-xs text-gray-600">{{ __('Daily total P&L, simulated.') }}</div>
                </div>
                <div class="text-xs text-gray-600 tabular-nums">
                    {{ __('Best') }}:
                    @php $best = $analytics['best'] ?? null; @endphp
                    <span class="font-semibold text-emerald-700">{{ $best ? number_format((float) $best->pnl, 2) : '-' }}</span>
                    <span class="text-gray-400">/</span>
                    {{ __('Worst') }}:
                    @php $worst = $analytics['worst'] ?? null; @endphp
                    <span class="font-semibold text-red-700">{{ $worst ? number_format((float) $worst->pnl, 2) : '-' }}</span>
                </div>
            </div>

            <div
                class="mt-3"
                x-data="{
                    labels: @js($analytics['daily_labels'] ?? []),
                    values: @js($analytics['daily_pnl'] ?? []),
                    draw() {
                        const canvas = this.$refs.c;
                        if (!canvas) return;
                        const ctx = canvas.getContext('2d');
                        const w = canvas.width = canvas.clientWidth * (window.devicePixelRatio || 1);
                        const h = canvas.height = 140 * (window.devicePixelRatio || 1);
                        const dpr = (window.devicePixelRatio || 1);
                        ctx.scale(dpr, dpr);

                        const vals = this.values.map(v => Number(v || 0));
                        const min = Math.min(...vals, 0);
                        const max = Math.max(...vals, 0);
                        const padX = 10, padY = 12;
                        const innerW = canvas.clientWidth - padX * 2;
                        const innerH = 140 - padY * 2;

                        const xAt = (i) => padX + (innerW * (vals.length <= 1 ? 0 : (i / (vals.length - 1))));
                        const yAt = (v) => {
                            if (max === min) return padY + innerH / 2;
                            return padY + (innerH * (1 - ((v - min) / (max - min))));
                        };

                        // background
                        ctx.clearRect(0, 0, canvas.clientWidth, 140);
                        ctx.lineWidth = 1;
                        ctx.strokeStyle = 'rgba(15, 23, 42, 0.08)';
                        for (let g = 0; g <= 4; g++) {
                            const y = padY + innerH * (g / 4);
                            ctx.beginPath();
                            ctx.moveTo(padX, y);
                            ctx.lineTo(padX + innerW, y);
                            ctx.stroke();
                        }

                        // zero line
                        const y0 = yAt(0);
                        ctx.strokeStyle = 'rgba(15, 23, 42, 0.18)';
                        ctx.beginPath();
                        ctx.moveTo(padX, y0);
                        ctx.lineTo(padX + innerW, y0);
                        ctx.stroke();

                        // line
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = 'rgba(16, 185, 129, 0.9)';
                        ctx.beginPath();
                        vals.forEach((v, i) => {
                            const x = xAt(i);
                            const y = yAt(v);
                            if (i === 0) ctx.moveTo(x, y);
                            else ctx.lineTo(x, y);
                        });
                        ctx.stroke();

                        // points
                        ctx.fillStyle = 'rgba(16, 185, 129, 0.9)';
                        vals.forEach((v, i) => {
                            const x = xAt(i);
                            const y = yAt(v);
                            ctx.beginPath();
                            ctx.arc(x, y, 2.2, 0, Math.PI * 2);
                            ctx.fill();
                        });
                    }
                }"
                x-init="draw(); window.addEventListener('resize', () => draw())"
            >
                <canvas x-ref="c" class="w-full"></canvas>
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
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-full ring-1 ring-slate-900/10 flex items-center justify-center text-[10px] font-bold text-white"
                                :style="`background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.35), rgba(255,255,255,0) 55%), hsl(${(s.charCodeAt(0)*19 + s.charCodeAt(1)*7) % 360} 70% 45%)`"
                            >
                                <span x-text="s.slice(0,1)"></span>
                            </div>
                            <div class="font-semibold text-gray-900" x-text="s"></div>
                        </div>
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
                <div class="text-sm text-gray-600">{{ __('Shows simulated buy/sell time, long/short, and P&L per trade.') }}</div>
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
                        <th class="py-2 pr-4">{{ __('Buy time') }}</th>
                        <th class="py-2 pr-4">{{ __('Sell time') }}</th>
                        <th class="py-2 pr-4">{{ __('Pair') }}</th>
                        <th class="py-2 pr-4">{{ __('Side') }}</th>
                        <th class="py-2 pr-4">{{ __('Qty') }}</th>
                        <th class="py-2 pr-4">{{ __('Entry') }}</th>
                        <th class="py-2 pr-4">{{ __('Exit') }}</th>
                        <th class="py-2 pr-4">{{ __('Risk') }}</th>
                        <th class="py-2 pr-4">{{ __('Liquidity range') }}</th>
                        <th class="py-2 pr-4">{{ __('Pool size') }}</th>
                        <th class="py-2 pr-4">{{ __('P&L') }}</th>
                        <th class="py-2 pr-4">{{ __('P&L %') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trades as $t)
                        @php
                            $pair = ($t->symbol ?? '').'/'.($t->pair ?? 'USDT');
                            $pnl = (float) $t->pnl;
                            $side = strtoupper((string) ($t->side ?? ''));
                            $risk = strtoupper((string) ($t->risk_level ?? ''));
                        @endphp
                        <tr class="border-b border-slate-900/5">
                            <td class="py-2 pr-4">{{ $t->trade_date?->toDateString() }}</td>
                            <td class="py-2 pr-4 tabular-nums whitespace-nowrap">
                                {{ $t->opened_at ? $t->opened_at->timezone($tz ?? config('app.timezone'))->format('Y-m-d H:i:s') : '-' }}
                            </td>
                            <td class="py-2 pr-4 tabular-nums whitespace-nowrap">
                                {{ $t->closed_at ? $t->closed_at->timezone($tz ?? config('app.timezone'))->format('Y-m-d H:i:s') : '-' }}
                            </td>
                            <td class="py-2 pr-4 font-medium">
                                <div class="flex items-center gap-2">
                                    @php
                                        $sym = strtoupper((string) ($t->symbol ?? ''));
                                        $h = sprintf('%u', crc32($sym));
                                        $hue = ((int) $h) % 360;
                                    @endphp
                                    <div
                                        class="w-7 h-7 rounded-full ring-1 ring-slate-900/10 flex items-center justify-center text-[10px] font-bold text-white"
                                        style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.35), rgba(255,255,255,0) 55%), hsl({{ $hue }} 70% 45%)"
                                        title="{{ $sym }}"
                                    >
                                        {{ mb_substr($sym, 0, 1) }}
                                    </div>
                                    <div>{{ $pair }}</div>
                                </div>
                            </td>
                            <td class="py-2 pr-4">
                                @if ($side === 'LONG')
                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">LONG</span>
                                @elseif ($side === 'SHORT')
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">SHORT</span>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-2 pr-4 tabular-nums">{{ number_format((float) $t->qty, 6) }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ number_format((float) $t->buy_price, 6) }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ number_format((float) $t->sell_price, 6) }}</td>
                            <td class="py-2 pr-4">
                                @if ($risk)
                                    <span class="text-xs font-semibold {{ $risk === 'HIGH' ? 'text-red-700' : ($risk === 'MEDIUM' ? 'text-amber-700' : 'text-emerald-700') }}">
                                        {{ $risk }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-2 pr-4 tabular-nums">
                                @if ($t->liquidity_range_low !== null && $t->liquidity_range_high !== null)
                                    {{ number_format((float) $t->liquidity_range_low, 6) }} – {{ number_format((float) $t->liquidity_range_high, 6) }}
                                    @if ($t->fee_tier_bps)
                                        <span class="text-xs text-gray-500">({{ (int) $t->fee_tier_bps }} bps)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 pr-4 tabular-nums">
                                @if ($t->pool_size_usd !== null)
                                    ${{ number_format((float) $t->pool_size_usd, 0) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 pr-4 font-semibold {{ $pnl < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ number_format($pnl, 2) }}
                            </td>
                            <td class="py-2 pr-4 tabular-nums {{ (float) $t->pnl_pct < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ number_format((float) $t->pnl_pct, 2) }}%
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-2 text-gray-600" colspan="13">{{ __('No trades yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

