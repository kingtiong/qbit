<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quantum Machines (QPU)</h2>
                <div class="text-sm text-gray-600">
                    Choose a machine to automate your quantum trading strategy.
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Deposit
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-200">
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-6 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white bg-gradient-to-r from-indigo-900 via-gray-900 to-indigo-900">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm text-indigo-100">Funding → Machine Purchase → Daily Rewards (UTC+8)</div>
                            <div class="mt-1 text-2xl font-semibold">Buy a Quantum Machine to start daily QOS</div>
                            <div class="mt-2 text-sm text-indigo-100 max-w-3xl">
                                Deposits are credited to your <span class="font-medium">Registered Wallet</span>.
                                All earnings and commissions are credited to your <span class="font-medium">Commission Wallet</span>.
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-[260px]">
                            <div class="p-4 rounded border border-white/20 bg-white/10">
                                <div class="text-xs text-indigo-100 uppercase tracking-wider">Registered Wallet</div>
                                <div class="mt-1 text-xl font-semibold">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-indigo-100">Used to buy machines</div>
                            </div>
                            <div class="p-4 rounded border border-white/20 bg-white/10">
                                <div class="text-xs text-indigo-100 uppercase tracking-wider">Commission Wallet</div>
                                <div class="mt-1 text-xl font-semibold">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                                <div class="mt-1 text-xs text-indigo-100">Daily QOS + network</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-sm text-indigo-100">
                        Daily distribution: <span class="font-medium">UTC+8</span> (system schedule).
                        @if ($todayRate)
                            <span class="ml-2 opacity-80">Legacy ROI reference (admin-set): {{ bcmul((string) $todayRate->rate, '100', 2) }}% for {{ $today->toDateString() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($packages as $package)
                    @php
                        $maxReturn = (float) ($package->amount ?? 0) * (float) ($package->max_return_multiplier ?? 0);
                        $canBuy = (float) $registeredWallet->balance >= (float) $package->amount;
                    @endphp
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="inline-flex items-center px-2 py-1 rounded text-xs border bg-indigo-50 border-indigo-200 text-indigo-800">
                                        {{ $package->code ?? 'QPU' }}
                                    </div>
                                    <div class="mt-2 text-xl font-semibold text-gray-900">{{ $package->label }}</div>
                                    @if ($package->summary)
                                        <div class="mt-1 text-sm text-gray-600">{{ $package->summary }}</div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500 uppercase tracking-wider">Capital</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $package->currency }} {{ number_format((float) $package->amount, 2) }}</div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="p-3 rounded border bg-gray-50">
                                    <div class="text-xs text-gray-500 uppercase tracking-wider">Daily QOS</div>
                                    <div class="mt-1 font-semibold text-gray-900">{{ $package->currency }} {{ number_format((float) ($package->daily_qos_amount ?? 0), 2) }}</div>
                                </div>
                                <div class="p-3 rounded border bg-gray-50">
                                    <div class="text-xs text-gray-500 uppercase tracking-wider">Max Return</div>
                                    <div class="mt-1 font-semibold text-gray-900">
                                        {{ number_format((float) ($package->max_return_multiplier ?? 0), 2) }}x
                                        <span class="text-xs text-gray-600">({{ $package->currency }} {{ number_format($maxReturn, 2) }})</span>
                                    </div>
                                </div>
                            </div>

                            @if (is_array($package->benefits) && count($package->benefits))
                                <ul class="mt-4 text-sm text-gray-700 space-y-1">
                                    @foreach ($package->benefits as $b)
                                        <li class="flex gap-2">
                                            <span class="mt-2 h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                            <span>{{ $b }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-5 flex items-center justify-between gap-3">
                                <div class="text-xs text-gray-600">
                                    Deducts from <span class="font-medium">Registered Wallet</span>.
                                </div>
                                <form method="POST" action="{{ route('investments.store') }}">
                                    @csrf
                                    <input type="hidden" name="investment_package_id" value="{{ $package->id }}" />
                                    <x-primary-button :disabled="!$canBuy">
                                        {{ $canBuy ? 'Buy Machine' : 'Insufficient Funds' }}
                                    </x-primary-button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-600">No packages configured.</div>
                @endforelse
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-lg font-medium text-gray-900">Leader Node (QBP)</div>
                    <div class="mt-1 text-sm text-gray-600">
                        The Partnership Node is designed for leaders: buy a node position, market the ecosystem, earn network rewards, and share the global pool.
                        (Admin-managed in this MVP.)
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
