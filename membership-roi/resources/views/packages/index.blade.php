<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quantum Machines (QPU)') }}</h2>
                <div class="text-sm text-gray-600">
                    {{ __('Choose a machine to automate your quantum trading strategy.') }}
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('wallet.index') }}" class="btn-dark normal-case text-sm">
                    {{ __('Deposit') }}
                </a>
                <a href="{{ route('dashboard') }}" class="btn-neutral normal-case text-sm">
                    {{ __('Dashboard') }}
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
                            <div class="text-sm text-slate-700">{{ __('Funding → Machine Purchase → Daily Rewards (UTC+8)') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ __('Activate a Quantum Machine to start daily QOS') }}</div>
                            <div class="mt-2 text-sm text-slate-700 max-w-3xl">
                                {{ __('Deposits are credited to your Registered Wallet.') }}
                                {{ __('All earnings and commissions are credited to your Quant Wallet.') }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full sm:min-w-[260px] sm:max-w-sm">
                            <div class="p-4 surface-solid">
                                <div class="text-xs text-slate-600 uppercase tracking-wider">{{ __('Registered Wallet') }}</div>
                                <div class="mt-1 text-xl font-semibold text-slate-900">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-slate-600">{{ __('Used to activate machines') }}</div>
                            </div>
                            <div class="p-4 surface-solid">
                                <div class="text-xs text-slate-600 uppercase tracking-wider">{{ __('Quant Wallet') }}</div>
                                <div class="mt-1 text-xl font-semibold text-slate-900">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-slate-600">{{ __('Daily QOS + network') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-sm text-slate-700">
                        {{ __('Daily distribution') }}: <span class="font-medium">UTC+8</span> ({{ __('system schedule') }}).
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($packages as $package)
                    @php
                        $canBuyRegistered = (float) $registeredWallet->balance >= (float) $package->amount;
                        $canBuyQuant = (float) $commissionWallet->balance >= (float) $package->amount;
                        $canBuyAny = $canBuyRegistered || $canBuyQuant;
                        $totalUnits = (int) ($package->total_units ?? 100);
                        $soldUnits = (int) ($package->sold_units ?? 0);
                        $remainingUnits = max(0, $totalUnits - $soldUnits);
                    @endphp
                    <div class="surface">
                        <div class="p-6" x-data="{ open: false }">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="inline-flex items-center px-2 py-1 rounded text-xs border bg-amber-500/10 border-amber-300/30 text-amber-200">
                                        {{ $package->code ?? 'QPU' }}
                                    </div>
                                    <div class="mt-2 text-xl font-semibold text-gray-900">{{ $package->label }}</div>
                                    @php
                                        $summary = (string) ($package->summary ?? '');
                                        $hideReturnCopy = (bool) preg_match('/\b\d+(\.\d+)?x\b/i', $summary) || (bool) preg_match('/max\s*return/i', $summary);
                                    @endphp
                                    @if ($summary !== '' && !$hideReturnCopy)
                                        <div class="mt-1 text-sm text-gray-600">{{ $summary }}</div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Capital') }}</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $package->currency }} {{ number_format((float) $package->amount, 2) }}</div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-3">
                                <div class="p-3 surface-muted">
                                    <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Daily QOS') }}</div>
                                    <div class="mt-1 font-semibold text-gray-900">{{ $package->currency }} {{ number_format((float) ($package->daily_qos_amount ?? 0), 2) }}</div>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-xs text-gray-600">
                                <div>{{ __('Units') }}: <span class="font-semibold text-gray-900">{{ $remainingUnits }}</span> / {{ $totalUnits }}</div>
                                @if ($remainingUnits <= 0)
                                    <div class="font-semibold text-red-600">{{ __('Sold out') }}</div>
                                @endif
                            </div>

                            @php
                                $benefits = is_array($package->benefits) ? $package->benefits : [];
                                $benefits = array_values(array_filter($benefits, function ($b) {
                                    $s = (string) $b;
                                    if (preg_match('/\b\d+(\.\d+)?x\b/i', $s)) return false;
                                    if (preg_match('/max\s*return/i', $s)) return false;
                                    return true;
                                }));
                            @endphp
                            @if (count($benefits))
                                <ul class="mt-4 text-sm text-gray-700 space-y-1">
                                    @foreach ($benefits as $b)
                                        <li class="flex gap-2">
                                            <span class="mt-2 h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                            <span>{{ $b }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-5 flex items-center justify-between gap-3">
                                <div class="text-xs text-gray-600">
                                    {{ __('Choose payment wallet') }}
                                </div>
                                <button
                                    type="button"
                                    class="btn-dark px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-50"
                                    @click="open = true"
                                    @disabled(!$canBuyAny || $remainingUnits <= 0)
                                >
                                    {{ $canBuyAny ? __('Activate') : __('Insufficient Funds') }}
                                </button>
                            </div>

                            <!-- Payment modal -->
                            <div
                                x-show="open"
                                x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                aria-modal="true"
                                role="dialog"
                            >
                                <div class="absolute inset-0 bg-slate-900/40" @click="open = false"></div>
                                <div class="relative w-full max-w-lg surface p-6">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ __('Choose payment wallet') }}</div>
                                            <div class="text-sm text-gray-600">{{ $package->label }} • {{ $package->currency }} {{ number_format((float) $package->amount, 2) }}</div>
                                        </div>
                                        <button type="button" class="btn-neutral text-sm normal-case" @click="open = false">{{ __('Close') }}</button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                        <form method="POST" action="{{ route('investments.store') }}" class="surface-muted p-4">
                                            @csrf
                                            <input type="hidden" name="investment_package_id" value="{{ $package->id }}" />
                                            <input type="hidden" name="wallet_type" value="registered" />
                                            <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Registered Wallet') }}</div>
                                            <div class="mt-1 text-lg font-semibold text-gray-900 tabular-nums">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                                            <div class="mt-2 text-xs text-gray-600">{{ __('Used to activate machines') }}</div>
                                            <button type="submit" class="btn-dark w-full mt-3 normal-case text-sm" @disabled(!$canBuyRegistered)>{{ __('Pay with Registered') }}</button>
                                        </form>

                                        <form method="POST" action="{{ route('investments.store') }}" class="surface-muted p-4">
                                            @csrf
                                            <input type="hidden" name="investment_package_id" value="{{ $package->id }}" />
                                            <input type="hidden" name="wallet_type" value="commission" />
                                            <div class="text-xs text-gray-600 uppercase tracking-wider">{{ __('Quant Wallet') }}</div>
                                            <div class="mt-1 text-lg font-semibold text-gray-900 tabular-nums">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                                            <div class="mt-2 text-xs text-gray-600">{{ __('Daily QOS + network') }}</div>
                                            <button type="submit" class="btn-dark w-full mt-3 normal-case text-sm" @disabled(!$canBuyQuant)>{{ __('Pay with Quant') }}</button>
                                        </form>
                                    </div>

                                    <div class="mt-4 text-xs text-gray-600">
                                        {{ __('Note') }}: {{ __('Max 10 active QPU per user. Units are limited and deducted in real time.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-600">{{ __('No packages configured.') }}</div>
                @endforelse
            </div>

            <div class="mt-8 surface">
                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-lg font-medium text-gray-900">{{ __('Genesis Node Network') }}</div>
                            <div class="mt-1 text-sm text-gray-600">
                                {{ __('Join QBP from Tier 1 upward. Price increases by tier; amounts are integer-only (no cents).') }}
                            </div>
                        </div>
                        <a href="{{ route('qbp.index') }}" class="btn-neutral normal-case text-sm">{{ __('Join QBP') }}</a>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
