<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('QBP') }}</h2>
                <div class="text-sm text-gray-600">{{ __('25-tier allocation • FIFO purchase from Tier 1 upward') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('packages.index') }}" class="btn-neutral normal-case text-sm">{{ __('Packages') }}</a>
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
                <div class="text-sm text-gray-600">{{ __('How pricing works') }}</div>
                <div class="mt-1 text-sm text-gray-700">
                    <div>- {{ __('Tier 1 price: 300 USDT / unit') }}</div>
                    <div>- {{ __('Price increases 20% each tier') }}</div>
                    <div>- {{ __('Units decrease 10% each tier') }}</div>
                    <div>- {{ __('All purchases are integer-only (no cents)') }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('qbp.purchase') }}" class="mt-5 flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <x-input-label for="units" :value="__('Units to buy')" />
                <x-text-input id="units" name="units" type="number" min="1" step="1" class="mt-1 block w-48" :value="old('units')" required />
                <x-input-error class="mt-2" :messages="$errors->get('units')" />
            </div>
            <x-primary-button>{{ __('Buy QBP') }}</x-primary-button>
            <div class="text-xs text-gray-600">
                {{ __('Your order will fill from the lowest available tier(s) automatically.') }}
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="surface p-6">
            <div class="text-lg font-medium mb-1">{{ __('QBP Tiers') }}</div>
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
                            <tr><td class="py-3 text-gray-600" colspan="4">{{ __('No QBP tiers configured.') }}</td></tr>
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
                            <tr><td class="py-3 text-gray-600" colspan="6">{{ __('No purchases yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

