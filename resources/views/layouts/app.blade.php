<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-seo :title="$seoTitle ?? null" :description="$seoDescription ?? null" :image="$seoImage ?? null" :og-description="$ogDescription ?? null" />

        <meta name="theme-color" content="#2C3531">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300..700&family=Inter:wght@300..700&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet" />

        @stack('head')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-primary-dark text-text-mint">
        <div class="min-h-screen flex flex-col">
            <x-navbar :transparent="$transparentNav" />

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-primary-dark shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-footer />
        </div>

        {{-- Storefront search drawer: opened by the navbar icon or the "/" key --}}
        <livewire:global-search />

        <x-toast-container />
        <x-cookie-consent />

        @livewireScripts
        @stack('scripts')
    </body>
</html>
