@props([
    'href' => null,
    'variant' => 'filled',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-8 py-3.5 text-xs font-sans uppercase tracking-[0.2em] transition duration-300';

    $variants = [
        'filled' => 'bg-primary-teal text-white hover:bg-primary-teal/80 hover:shadow-lg hover:shadow-primary-teal/30',
        'outline' => 'border border-accent-gold text-accent-gold hover:bg-accent-gold hover:text-primary-dark',
        'ghost' => 'text-accent-gold hover:text-accent-peach',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['filled']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>
        {{ $slot }}
    </button>
@endif
