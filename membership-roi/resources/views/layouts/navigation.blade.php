<nav class="topbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-3">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('qbp.index') }}" class="inline-flex items-center">
                        <x-application-logo class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Top Menu: 3 rows, left -> right -->
                <div class="grid grid-cols-2 gap-2">
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
                        {{ __('Q-Flash (Auto Trade)') }}
                    </x-nav-link>
                    <x-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')">
                        {{ __('Wallet') }}
                    </x-nav-link>
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
        </div>
    </div>
</nav>
