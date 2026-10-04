{{--
    Bare layout for the ÉLITE auth pages: keeps Filament's base document
    (styles, Livewire, notifications) but drops the default simple-page
    card, topbar and footer so the login view owns the whole viewport.
--}}
<x-filament-panels::layout.base :livewire="$livewire">
    @push('styles')
        {{-- Fonts come from the panel-wide HEAD_START hook (AdminPanelProvider). --}}
        <link rel="preconnect" href="https://images.unsplash.com" crossorigin />
        <link
            rel="preload"
            as="image"
            href="https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=1600&q=90"
            media="(min-width: 1024px)"
            fetchpriority="high"
        />
        <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}?v={{ @filemtime(public_path('css/admin-login.css')) }}" />
    @endpush

    {{ $slot }}
</x-filament-panels::layout.base>
