<aside class="hidden md:block w-64 shrink-0">
    <div class="surface-muted p-3 sticky top-20">
        <div class="space-y-2">
            <x-responsive-nav-link :href="route('qbp.index')" :active="request()->routeIs('qbp.*') || request()->routeIs('gbp.*')">
                {{ __('QBP') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('network.index')" :active="request()->routeIs('network.*')">
                {{ __('Network') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')">
                {{ __('Package (QPU)') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('autotrade.index')" :active="request()->routeIs('autotrade.*')">
                {{ __('Q-Flash') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')">
                {{ __('Wallet') }}
            </x-responsive-nav-link>
        </div>
    </div>
</aside>

