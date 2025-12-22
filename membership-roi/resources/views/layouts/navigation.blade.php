<nav x-data="{ open: false }" class="topbar">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div class="flex items-center gap-2">
                <div class="mr-1 flex items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="px-2 py-1 rounded-lg text-xs ring-1 ring-amber-300/20 hover:bg-white/5 {{ app()->getLocale() === 'en' ? 'bg-amber-500/10 text-amber-50' : 'text-amber-50/70' }}">
                        EN
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'zh_CN']) }}" class="px-2 py-1 rounded-lg text-xs ring-1 ring-amber-300/20 hover:bg-white/5 {{ app()->getLocale() === 'zh_CN' ? 'bg-amber-500/10 text-amber-50' : 'text-amber-50/70' }}">
                        中文
                    </a>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-xl text-amber-50/80 bg-white/5 hover:bg-white/10 hover:text-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-black transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-amber-50/80 hover:text-amber-50 bg-white/5 hover:bg-white/10 ring-1 ring-amber-300/20 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-black transition duration-150 ease-in-out" aria-label="Menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Slide-down Menu (all screen sizes) -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.outside="open = false"
        class="border-t border-amber-300/15 bg-black/95 backdrop-blur-xl"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')">
                {{ __('Packages') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('qbp.index')" :active="request()->routeIs('qbp.*') || request()->routeIs('gbp.*')">
                {{ __('QBP') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('autotrade.index')" :active="request()->routeIs('autotrade.*')">
                {{ __('Auto Trade') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')">
                {{ __('Wallet') }}
            </x-responsive-nav-link>
            </div>

            <div class="mt-4 pt-4 border-t border-white/10">
                <div class="text-sm text-amber-50/80">
                    <div class="font-medium text-amber-50">{{ Auth::user()->name }}</div>
                    <div class="text-amber-50/60">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
