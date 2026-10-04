<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ÉLITE') }}</title>

        <meta name="theme-color" content="#2C3531">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300..700&family=Inter:wght@300..700&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet" />

        @php
            // The vault pane rotates between photographs (Unsplash; replace with the maison's own).
            // Only dials from houses ÉLITE carries, or unbranded — no other makers' names beside the form.
            $vaultImage = \Illuminate\Support\Arr::random([
                'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=1600&q=85',
                'https://images.unsplash.com/photo-1547996160-81dfa63595aa?w=1600&q=85',
            ]);
        @endphp
        {{-- Above the fold on desktop: fetch the pane's photo immediately (skipped on small screens, where the pane is hidden) --}}
        <link rel="preload" as="image" href="{{ $vaultImage }}" fetchpriority="high" media="(min-width: 1024px)">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-primary-dark text-text-mint">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

            <!-- Left: the vault (≥ 1024px) — shared by every auth page via this layout -->
            <aside class="vault-pane relative hidden overflow-hidden bg-primary-dark lg:block" aria-label="{{ __('ÉLITE') }}">
                {{-- <picture>: phones (pane hidden) receive a 1px placeholder instead of the photograph --}}
                <picture>
                    <source media="(min-width: 1024px)" srcset="{{ $vaultImage }}">
                    <img
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        alt="{{ __('A finely crafted timepiece') }}"
                        class="vault-kenburns absolute inset-0 h-full w-full object-cover object-center"
                        fetchpriority="high"
                        decoding="async"
                    >
                </picture>

                {{-- Mood: brand tint, then a fade that keeps the quote legible, then film grain --}}
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(44,53,49,0.72)_0%,rgba(17,100,102,0.55)_100%)]" aria-hidden="true"></div>
                <div class="absolute inset-0 bg-[linear-gradient(180deg,transparent_50%,rgba(44,53,49,0.95)_100%)]" aria-hidden="true"></div>
                <div class="vault-glow absolute inset-0" aria-hidden="true"></div>
                <div class="vault-grain absolute inset-0" aria-hidden="true"></div>

                {{-- Dust catching light in a vault --}}
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    @foreach ([[14, 22, 3, 0.45, '18s', '0s', '22px', '-30px'], [32, 64, 2, 0.35, '23s', '-6s', '-26px', '-18px'], [58, 18, 2, 0.4, '20s', '-11s', '18px', '28px'], [76, 48, 3, 0.3, '25s', '-3s', '-20px', '24px'], [44, 82, 2, 0.5, '16s', '-9s', '28px', '-14px'], [86, 76, 2, 0.35, '21s', '-14s', '-16px', '-32px'], [24, 40, 2, 0.3, '24s', '-18s', '30px', '12px']] as [$x, $y, $size, $opacity, $duration, $delay, $dx, $dy])
                        <span class="vault-speck" style="left: {{ $x }}%; top: {{ $y }}%; width: {{ $size }}px; height: {{ $size }}px; --o: {{ $opacity }}; --dx: {{ $dx }}; --dy: {{ $dy }}; animation-duration: {{ $duration }}; animation-delay: {{ $delay }};"></span>
                    @endforeach
                </div>

                {{-- Content in normal flow, so the middle block and the quote can never overlap --}}
                <div class="relative z-10 flex h-full min-h-screen flex-col justify-between">
                    <div class="px-14 pt-14">
                        <a href="{{ route('home') }}" class="inline-block">
                            <x-logo variant="white" size="sm" />
                        </a>
                        <span class="mt-5 block h-px w-10 bg-accent-gold/40" aria-hidden="true"></span>
                        <p class="mt-4 font-accent italic text-[11px] uppercase tracking-[0.4em] text-accent-gold">{{ __('Maison Horlogère') }}</p>
                    </div>

                    <div class="flex flex-1 items-center py-12 pl-20 pr-10">
                        <div>
                            <div class="flex w-20 items-center gap-2" aria-hidden="true">
                                <span class="h-px flex-1 bg-gradient-to-r from-transparent to-accent-gold"></span>
                                <span class="h-[5px] w-[5px] rotate-45 bg-accent-gold"></span>
                                <span class="h-px flex-1 bg-gradient-to-l from-transparent to-accent-gold"></span>
                            </div>
                            <p class="mt-6 font-accent italic text-[10px] uppercase tracking-[0.5em] text-accent-gold min-[1440px]:text-xs">{{ __('A private portal') }}</p>
                            <h2 class="mt-4 max-w-[440px] font-heading text-[41px] leading-[1.1] text-white min-[1440px]:text-5xl">{{ __('Time, kept in trust.') }}</h2>
                            <p class="mt-5 max-w-[400px] font-accent italic text-[15px] leading-relaxed text-text-mint/85 min-[1440px]:text-[17px]">
                                {{ __('Sign in to access your wishlist, follow your acquisitions, and continue conversations with our atelier.') }}
                            </p>
                            <span class="mt-8 block h-[60px] w-px bg-accent-gold/60" aria-hidden="true"></span>
                            <ul class="mt-6 space-y-2 font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold/75 min-[1440px]:text-[13px]">
                                <li>{{ __('Your acquisitions') }}</li>
                                <li>{{ __('Your wishlist') }}</li>
                                <li>{{ __('Your correspondence') }}</li>
                            </ul>
                        </div>
                    </div>

                    <figure class="relative mb-[60px] ml-20 max-w-sm pl-5">
                        <span class="absolute left-0 top-1.5 h-10 w-[2px] bg-accent-gold" aria-hidden="true"></span>
                        <blockquote class="font-accent italic text-xl leading-relaxed text-white/90">
                            &ldquo;{{ __('A watch is not simply worn — it is carried forward.') }}&rdquo;
                        </blockquote>
                        <figcaption class="mt-3 font-accent italic text-[11px] uppercase tracking-[0.4em] text-accent-gold">— {{ __('The Atelier') }}</figcaption>
                    </figure>
                </div>
            </aside>

            <!-- Right: form panel -->
            <div class="flex flex-col justify-center px-6 py-16 sm:px-12 lg:px-20">
                <div class="w-full max-w-sm mx-auto">
                    <a href="{{ route('home') }}" class="block mb-12 lg:hidden">
                        <x-logo variant="nav" size="md" />
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
