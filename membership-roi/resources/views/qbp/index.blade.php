<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leader Node (QBP)</h2>
                <div class="text-sm text-gray-600">Partnership node packages and eligibility details.</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('packages.index') }}" class="btn-neutral normal-case text-sm">QPU Packages</a>
                <a href="{{ route('dashboard') }}" class="btn-neutral normal-case text-sm">Dashboard</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-muted text-gray-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="surface p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="surface-muted p-4">
                <div class="text-sm text-gray-600">Registered Wallet</div>
                <div class="text-2xl font-semibold text-gray-900">USDT {{ number_format((float) $registeredWallet->balance, 2) }}</div>
                <div class="mt-1 text-xs text-gray-600">Used for QPU purchases.</div>
            </div>
            <div class="surface-muted p-4">
                <div class="text-sm text-gray-600">Commission Wallet</div>
                <div class="text-2xl font-semibold text-gray-900">USDT {{ number_format((float) $commissionWallet->balance, 2) }}</div>
                <div class="mt-1 text-xs text-gray-600">Earnings and network rewards.</div>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-700">
            QBP is the Leader/Partnership Node program. Packages below are shown for reference; purchase/activation can be handled by admin if enabled in your setup.
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($qbpPackages as $pkg)
            <div class="surface p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center px-2 py-1 rounded text-xs border bg-emerald-50 border-emerald-200 text-emerald-800">
                            QBP • Level {{ (int) $pkg->level }}
                        </div>
                        <div class="mt-2 text-xl font-semibold text-gray-900">{{ $pkg->code ?? 'QBP' }}</div>
                        <div class="mt-1 text-sm text-gray-600">Leader Node package</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Amount</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $pkg->currency }} {{ number_format((float) $pkg->amount, 2) }}</div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="surface-muted p-3">
                        <div class="text-xs text-gray-600 uppercase tracking-wider">Group %</div>
                        <div class="mt-1 font-semibold text-gray-900">{{ number_format((float) $pkg->group_percent, 3) }}</div>
                    </div>
                    <div class="surface-muted p-3">
                        <div class="text-xs text-gray-600 uppercase tracking-wider">Holder limit</div>
                        <div class="mt-1 font-semibold text-gray-900">{{ (int) ($pkg->holder_limit ?? 0) }}</div>
                    </div>
                </div>

                <div class="mt-4 text-xs text-gray-600">
                    Global denom: <span class="font-medium">{{ (int) ($pkg->global_denom ?? 0) }}</span>
                </div>

                @php
                    $alreadyOwned = in_array($pkg->id, $myActivePackageIds ?? [], true);
                    $canBuy = (float) $registeredWallet->balance >= (float) $pkg->amount;
                @endphp

                <div class="mt-5 flex items-center justify-between gap-3">
                    <div class="text-xs text-gray-600">
                        Deducts from <span class="font-medium">Registered Wallet</span>.
                    </div>

                    <form method="POST" action="{{ route('qbp.purchase', $pkg) }}">
                        @csrf
                        <x-primary-button :disabled="$alreadyOwned || !$canBuy">
                            {{ $alreadyOwned ? 'Owned' : ($canBuy ? 'Buy QBP' : 'Insufficient Funds') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-sm text-gray-600">No QBP packages configured.</div>
        @endforelse
    </div>
</x-app-layout>

