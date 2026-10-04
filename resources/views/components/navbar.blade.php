@props(['transparent' => false])

<nav
    x-data="{ open: false, scrolled: false }"
    @if ($transparent)
        x-init="scrolled = window.scrollY > 40; window.addEventListener('scroll', () => scrolled = window.scrollY > 40)"
        :class="scrolled ? 'bg-primary-dark/95 backdrop-blur-md border-b border-primary-teal/30 shadow-lg shadow-black/20' : 'bg-transparent border-b border-transparent'"
        class="fixed top-0 inset-x-0 z-50 transition-colors duration-300"
    @else
        x-init="scrolled = true"
        class="sticky top-0 z-50 bg-primary-dark/95 backdrop-blur-md border-b border-primary-teal/30"
    @endif
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo/elite-nav.svg') }}" alt="ÉLITE" class="h-7 lg:h-9 w-auto">
                </a>
            </div>

            <!-- Primary Navigation Links -->
            <div class="hidden lg:flex lg:items-center lg:space-x-10">
                <a href="{{ route('home') }}" class="font-sans text-xs uppercase tracking-[0.15em] transition {{ request()->routeIs('home') ? 'text-accent-gold' : 'text-text-mint hover:text-accent-peach' }}">
                    {{ __('Home') }}
                </a>
                <a href="{{ route('shop.index') }}" class="font-sans text-xs uppercase tracking-[0.15em] transition {{ request()->routeIs('shop.*') ? 'text-accent-gold' : 'text-text-mint hover:text-accent-peach' }}">
                    {{ __('Shop') }}
                </a>
                <a href="{{ route('brands.index') }}" class="font-sans text-xs uppercase tracking-[0.15em] transition {{ request()->routeIs('brands.*') ? 'text-accent-gold' : 'text-text-mint hover:text-accent-peach' }}">
                    {{ __('Brands') }}
                </a>
                <a href="{{ route('about') }}" class="font-sans text-xs uppercase tracking-[0.15em] transition {{ request()->routeIs('about') ? 'text-accent-gold' : 'text-text-mint hover:text-accent-peach' }}">
                    {{ __('About') }}
                </a>
                <a href="{{ route('contact.index') }}" class="font-sans text-xs uppercase tracking-[0.15em] transition {{ request()->routeIs('contact.*') ? 'text-accent-gold' : 'text-text-mint hover:text-accent-peach' }}">
                    {{ __('Contact') }}
                </a>
            </div>

            <!-- Icons -->
            <div class="hidden lg:flex lg:items-center lg:space-x-6">
                <!-- Search -->
                <button type="button" @click="$dispatch('open-search', { trigger: $el })" class="text-text-mint hover:text-accent-peach transition" aria-label="{{ __('Search') }}" aria-haspopup="dialog" title="{{ __('Search') }} ( / )">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </button>

                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="text-text-mint hover:text-accent-peach transition" aria-label="{{ __('Wishlist') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </a>

                <!-- Cart -->
                <livewire:cart-badge />

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48" content-classes="py-1 bg-primary-dark border border-primary-teal/30">
                    <x-slot name="trigger">
                        <button type="button" class="text-text-mint hover:text-accent-peach transition" aria-label="{{ __('Account') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @auth
                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('My Account') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('account.orders')">
                                {{ __('Orders') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('wishlist.index')">
                                {{ __('Wishlist') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @else
                            <x-dropdown-link :href="route('login')">
                                {{ __('Log In') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('register')">
                                {{ __('Register') }}
                            </x-dropdown-link>
                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Menu Button -->
            <div class="-me-2 flex items-center gap-1 lg:hidden">
                <button type="button" @click="open = false; $dispatch('open-search', { trigger: $el })" class="inline-flex h-11 w-11 items-center justify-center text-text-mint transition hover:text-accent-gold" aria-label="{{ __('Search') }}" aria-haspopup="dialog">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </button>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-text-mint hover:text-accent-gold focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden lg:hidden bg-primary-dark/98 backdrop-blur-md border-t border-primary-teal/30" x-cloak>
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Shop') }}</a>
            <a href="{{ route('brands.index') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Brands') }}</a>
            <a href="{{ route('about') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('About') }}</a>
            <a href="{{ route('contact.index') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Contact') }}</a>
        </div>

        <div class="pt-4 pb-3 border-t border-primary-teal/30">
            <div class="flex items-center px-4 space-x-6">
                <a href="{{ route('wishlist.index') }}" class="text-text-mint hover:text-accent-gold" aria-label="{{ __('Wishlist') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </a>
                <livewire:cart-badge />
            </div>

            @auth
                <div class="mt-3 px-4">
                    <div class="font-medium text-base text-text-mint">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-text-mint/70">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('My Account') }}</a>
                    <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Orders') }}</a>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="block px-4 py-2 text-text-mint hover:text-accent-gold">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Log In') }}</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 text-text-mint hover:text-accent-gold">{{ __('Register') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
