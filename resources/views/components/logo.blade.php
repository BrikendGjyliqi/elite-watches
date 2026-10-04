@props([
    'variant' => 'nav',
    'size' => 'md',
])

@php
    $variants = [
        'nav' => 'images/logo/elite-nav.svg',
        'primary' => 'images/logo/elite-primary.svg',
        'gold' => 'images/logo/elite-gold.svg',
        'white' => 'images/logo/elite-white.svg',
    ];

    $sizes = [
        'sm' => 'h-6',
        'md' => 'h-8',
        'lg' => 'h-12',
    ];

    $path = $variants[$variant] ?? $variants['nav'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $classes = $attributes->has('class') ? $attributes->get('class') : "{$sizeClass} w-auto";
@endphp

<img
    {{ $attributes->except('class') }}
    src="{{ asset($path) }}"
    alt="{{ config('app.name', 'ÉLITE') }}"
    class="{{ $classes }}"
>
