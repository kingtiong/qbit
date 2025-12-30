<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('QBP') }}</h2>
                <div class="text-sm text-gray-600">{{ __('25-tier allocation • FIFO purchase from Tier 1 upward') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('packages.index') }}" class="btn-neutral normal-case text-sm">{{ __('Package (QPU)') }}</a>
                <a href="{{ route('dashboard') }}" class="btn-neutral normal-case text-sm">{{ __('Dashboard') }}</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-gold">
            {{ session('status') }}
        </div>
    @endif

    <div class="surface p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="surface-muted p-4">
                <div class="text-sm text-gray-600">{{ __('Registered Wallet') }}</div>
                <div class="text-2xl font-semibold text-gray-900">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                <div class="mt-1 text-xs text-gray-600">{{ __('QBP purchases deduct from Registered Wallet.') }}</div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-sm text-gray-600">{{ __('How Genesis Node network Pricing Work') }}</div>
                <div class="mt-1 text-sm text-gray-700">
                    <div>- {{ __('Tier 1 price: 300 USDT / unit') }}</div>
                    <div>- {{ __('Price increases 20% each tier') }}</div>
                    <div>- {{ __('Units decrease 10% each tier') }}</div>
                    <div>- {{ __('All purchases are integer-only (no cents)') }}</div>
                </div>
            </div>
        </div>

        <div class="mt-5 surface-muted p-4">
            <div class="text-lg font-medium text-gray-900 mb-2">{{ __('Founder Benefit') }}</div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ __('Founder Team') }}</div>
                    <div class="text-xs text-gray-600">
                        {{ __('Only 30 members can join. Each member can choose either Pro (USDT 5,000) or Pro Max (USDT 10,000). QBP opens after 30/30 is filled.') }}
                    </div>
                </div>
                <div class="text-sm font-semibold text-gray-900">
                    {{ (int) ($foundingSold ?? 0) }}/{{ (int) ($foundingCap ?? 30) }}
                </div>
            </div>

            @php
                $cap = (int) ($foundingCap ?? 30);
                $sold = (int) ($foundingSold ?? 0);
                $pct = $cap > 0 ? min(100, (int) floor(($sold / $cap) * 100)) : 0;
            @endphp
            <div class="mt-3 h-2 w-full bg-black/10 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400/70" style="width: {{ $pct }}%"></div>
            </div>

            @if (!empty($myFounding))
                <div class="mt-3 text-sm text-gray-700">
                    {{ __('You already purchased:') }}
                    <span class="font-semibold">
                        {{ $myFounding->package === 'pro_max' ? __('Founder Pro Max (USDT 10,000)') : __('Founder Pro (USDT 5,000)') }}
                    </span>
                </div>
            @elseif (!($qbpUnlocked ?? false))
                <div class="mt-4 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('qbp.founding.purchase') }}">
                        @csrf
                        <input type="hidden" name="package" value="pro" />
                        <x-primary-button class="normal-case">{{ __('Founder Pro (USDT 5,000)') }}</x-primary-button>
                    </form>
                    <form method="POST" action="{{ route('qbp.founding.purchase') }}">
                        @csrf
                        <input type="hidden" name="package" value="pro_max" />
                        <x-primary-button class="normal-case">{{ __('Founder Pro Max (USDT 10,000)') }}</x-primary-button>
                    </form>
                </div>
            @else
                <div class="mt-3 text-sm text-gray-700">
                    {{ __('Founder Team complete. QBP is now unlocked.') }}
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('qbp.purchase') }}" class="mt-5 flex flex-wrap items-end gap-3">
            @csrf
            <input type="hidden" name="units" value="1" />
            <x-primary-button :disabled="!($qbpUnlocked ?? false)">{{ __('Join QBP') }}</x-primary-button>
            <div class="text-xs text-gray-600">
                @if (!($qbpUnlocked ?? false))
                    {{ __('Founder team is locked until reach 30/30.') }}
                @else
                    {{ __('Your order will fill from the lowest available tier(s) automatically.') }}
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="surface p-6">
            <div class="text-lg font-medium mb-1">{{ __('Genesis Node Network') }}</div>
            <div class="text-sm text-gray-600 mb-3">
                {{ __('Shown: current tier, next tier, and estimated final tier price.') }}
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-900/5">
                            <th class="py-2 pr-4">{{ __('Tier') }}</th>
                            <th class="py-2 pr-4">{{ __('Price (USDT)') }}</th>
                            <th class="py-2 pr-4">{{ __('Remaining units') }}</th>
                            <th class="py-2 pr-4">{{ __('Total units') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tiers as $t)
                            @php
                                $tierNum = (int) ($t['tier'] ?? 0);
                                $price = (int) ($t['unit_price'] ?? 0);
                                $total = (int) ($t['total_units'] ?? 0);
                                $rem = (int) ($t['remaining_units'] ?? 0);
                                $isFinal = isset($finalTier) && (int) ($finalTier['tier'] ?? 0) === $tierNum;
                                $isCurrent = isset($currentTier) && (int) ($currentTier['tier'] ?? 0) === $tierNum;
                                $isNext = isset($nextTier) && (int) ($nextTier['tier'] ?? 0) === $tierNum;
                            @endphp
                            <tr class="border-b border-slate-900/5">
                                <td class="py-2 pr-4 font-medium">
                                    {{ $tierNum }}
                                    @if ($isCurrent)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border bg-amber-500/10 border-amber-300/30 text-amber-200">{{ __('Current') }}</span>
                                    @elseif ($isNext)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border bg-white/5 border-white/10 text-amber-50/80">{{ __('Next') }}</span>
                                    @elseif ($isFinal)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border bg-white/5 border-white/10 text-amber-50/70">{{ __('Final') }}</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-4">
                                    USDT {{ number_format((float) $price, 0) }}
                                    @if ($isFinal)
                                        <div class="mt-1 text-[11px] text-amber-50/70">
                                            {{ __('This price is the estimated final price') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 pr-4">{{ $rem }}</td>
                                <td class="py-2 pr-4">{{ $total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-600" colspan="4">
                                    @if (!($qbpUnlocked ?? false))
                                        {{ __('Genesis node network is locked until Founder team reach 30/30.') }}
                                    @else
                                        {{ __('No QBP tiers configured.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="surface p-6">
            <div class="text-lg font-medium mb-3">{{ __('My recent QBP purchases') }}</div>
            <div class="text-sm text-gray-600 mb-3">
                {{ __('Current price') }}: <span class="font-semibold text-amber-50">USDT {{ number_format((float) ($currentUnitPrice ?? 0), 0) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-900/5">
                            <th class="py-2 pr-4">{{ __('Time') }}</th>
                            <th class="py-2 pr-4">{{ __('Tier') }}</th>
                            <th class="py-2 pr-4">{{ __('Units') }}</th>
                            <th class="py-2 pr-4">{{ __('Purchased price') }}</th>
                            <th class="py-2 pr-4">{{ __('Current price') }}</th>
                            <th class="py-2 pr-4">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($myPurchases as $p)
                            <tr class="border-b border-slate-900/5">
                                <td class="py-2 pr-4">{{ $p->purchased_at?->toDateTimeString() ?? $p->created_at }}</td>
                                <td class="py-2 pr-4">{{ (int) ($p->tier?->tier ?? 0) }}</td>
                                <td class="py-2 pr-4">{{ (int) $p->units }}</td>
                                <td class="py-2 pr-4">USDT {{ number_format((float) $p->unit_price, 0) }}</td>
                                <td class="py-2 pr-4">USDT {{ number_format((float) ($currentUnitPrice ?? 0), 0) }}</td>
                                <td class="py-2 pr-4 font-medium">USDT {{ number_format((float) $p->total_amount, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-600" colspan="6">
                                    @if (!($qbpUnlocked ?? false))
                                        {{ __('QBP is locked. No purchases available yet.') }}
                                    @else
                                        {{ __('No purchases yet.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

