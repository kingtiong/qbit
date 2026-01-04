<nav x-data="{ open: false }" class="topbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-3">
                <!-- Hamburger (3-line) -->
                <button
                    type="button"
                    @click="open = true"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-amber-50/80 hover:text-amber-50 bg-white/5 hover:bg-white/10 ring-1 ring-amber-300/20 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-black transition duration-150 ease-in-out"
                    aria-label="Menu"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route('qbp.index') }}" class="shrink-0 flex items-center">
                    <x-application-logo class="block h-9 w-auto" />
                </a>

                <!-- Desktop nav (so menu isn't "only Wallet") -->
                <div class="hidden lg:flex items-center gap-2 ml-2">
                    <x-nav-link :href="route('qbp.index')" :active="request()->routeIs('qbp.*') || request()->routeIs('gbp.*')">
                        {{ __('QBP') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('network.index')" :active="request()->routeIs('network.*')">
                        {{ __('Network') }}
                    </x-nav-link>
                    <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')">
                        {{ __('Package (QPU)') }}
                    </x-nav-link>
                    <x-nav-link :href="route('autotrade.index')" :active="request()->routeIs('autotrade.*')">
                        {{ __('Q-Flash') }}
                    </x-nav-link>
                    <x-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')">
                        {{ __('Wallet') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side: language + user -->
            <div class="flex items-center gap-2">
                <div class="mr-1 flex items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="px-2 py-1 rounded-lg text-xs ring-1 ring-amber-300/20 hover:bg-white/5 {{ app()->getLocale() === 'en' ? 'bg-amber-500/10 text-amber-50' : 'text-amber-50/70' }}">
                        EN
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'zh_CN']) }}" class="px-2 py-1 rounded-lg text-xs ring-1 ring-amber-300/20 hover:bg-white/5 {{ app()->getLocale() === 'zh_CN' ? 'bg-amber-500/10 text-amber-50' : 'text-amber-50/70' }}">
                        中文
                    </a>
                </div>

                <div class="hidden sm:block text-sm text-amber-50/80">
                    {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </div>

    <!-- Left slide-out drawer -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50"
        @keydown.escape.window="open = false"
    >
        <!-- backdrop -->
        <div class="absolute inset-0 bg-black/60" @click="open = false"></div>

        <!-- panel -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="absolute left-0 top-0 h-full w-[280px] max-w-[85vw] bg-black/95 backdrop-blur-xl border-r border-amber-300/15"
            @click.outside="open = false"
        >
            <div class="h-full flex flex-col">
                <div class="p-4 flex items-center justify-between border-b border-white/10">
                    <a href="{{ route('qbp.index') }}" class="flex items-center gap-2" @click="open = false">
                        <x-application-logo class="h-8 w-auto" />
                        <span class="text-sm font-semibold text-amber-50">{{ config('app.name', 'IQBIT') }}</span>
                    </a>
                    <button
                        type="button"
                        class="p-2 rounded-xl text-amber-50/80 hover:text-amber-50 bg-white/5 hover:bg-white/10 ring-1 ring-amber-300/20"
                        aria-label="Close"
                        @click="open = false"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-3 space-y-2 overflow-y-auto">
                    <x-responsive-nav-link :href="route('qbp.index')" :active="request()->routeIs('qbp.*') || request()->routeIs('gbp.*')" @click="open = false">
                        {{ __('QBP') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" @click="open = false">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('network.index')" :active="request()->routeIs('network.*')" @click="open = false">
                        {{ __('Network') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')" @click="open = false">
                        {{ __('Package (QPU)') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('autotrade.index')" :active="request()->routeIs('autotrade.*')" @click="open = false">
                        {{ __('Q-Flash (Auto Trade)') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')" @click="open = false">
                        {{ __('Wallet') }}
                    </x-responsive-nav-link>
                </div>

                <div class="mt-auto p-3 border-t border-white/10 space-y-2">
                    <div class="px-4 py-2 text-sm text-amber-50/80">
                        <div class="font-medium text-amber-50">{{ Auth::user()->name }}</div>
                        <div class="text-amber-50/60">{{ Auth::user()->email }}</div>
                    </div>

                    <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
