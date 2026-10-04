@props(['active' => 'overview', 'title' => null])

@php
    $navItems = [
        'overview' => ['label' => __('Overview'), 'href' => route('dashboard'), 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
        'orders' => ['label' => __('Acquisitions'), 'href' => route('account.orders'), 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
        'wishlist' => ['label' => __('Wishlist'), 'href' => route('wishlist.index'), 'icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z'],
        'addresses' => ['label' => __('Addresses'), 'href' => route('account.addresses'), 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
        'profile' => ['label' => __('Profile'), 'href' => route('profile.edit'), 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];
@endphp

<x-app-layout>

    <div class="bg-primary-dark min-h-screen pt-16 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($title)
                <div class="mb-12">
                    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('My Account') }}</p>
                    <h1 class="font-heading text-white text-4xl sm:text-5xl">{{ $title }}</h1>
                </div>
            @endif

            <div class="lg:flex lg:gap-10 lg:items-start">
                <!-- Sidebar -->
                <aside class="lg:w-64 shrink-0 mb-10 lg:mb-0">
                    <div class="mb-6 pb-6 border-b border-primary-teal/20">
                        <p class="text-sm text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-text-mint/50">{{ auth()->user()->email }}</p>
                    </div>

                    <nav class="flex lg:flex-col gap-1 overflow-x-auto scrollbar-hide -mx-1 px-1 lg:overflow-visible">
                        @foreach ($navItems as $key => $item)
                            <a
                                href="{{ $item['href'] }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs uppercase tracking-[0.15em] whitespace-nowrap transition {{ $active === $key ? 'bg-accent-gold/10 text-accent-gold border-l-2 border-accent-gold' : 'text-text-mint/70 hover:text-text-mint hover:bg-primary-teal/10 border-l-2 border-transparent' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        <form method="POST" action="{{ route('logout') }}" class="lg:mt-2">
                            @csrf
                            <button
                                type="submit"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs uppercase tracking-[0.15em] whitespace-nowrap text-text-mint/70 hover:text-red-400 hover:bg-red-400/5 border-l-2 border-transparent transition w-full"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </nav>
                </aside>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
