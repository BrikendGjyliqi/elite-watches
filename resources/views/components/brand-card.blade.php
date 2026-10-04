{{--
    A maison in the registry. Expects a Brand with `watches_count` and an optional
    `card_image` attribute (set by the controller). `compact` is the smaller strip variant.
--}}
@props(['brand', 'compact' => false])

@php
    $image = $brand->card_image ?? null;
    $count = (int) ($brand->watches_count ?? 0);
@endphp

<a
    href="{{ route('brands.show', $brand->slug) }}"
    {{ $attributes->class(['maison-card group relative flex flex-col overflow-hidden rounded bg-surface-card', 'h-[440px]' => ! $compact, 'h-[360px]' => $compact]) }}
>
    {{-- The house's representative piece, floating on a soft glow --}}
    <div @class(['relative overflow-hidden', 'h-[240px]' => ! $compact, 'h-[190px]' => $compact])>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(217,176,141,0.14)_0%,transparent_65%)]" aria-hidden="true"></div>
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $brand->name }}"
                class="maison-card__image absolute inset-0 h-full w-full object-contain p-6 drop-shadow-[0_18px_28px_rgba(0,0,0,0.55)]"
                loading="lazy"
                decoding="async"
            >
        @else
            <span class="absolute inset-0 grid place-items-center font-heading text-5xl text-accent-gold/20" aria-hidden="true">{{ mb_substr($brand->name, 0, 1) }}</span>
        @endif
        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-surface-card to-transparent" aria-hidden="true"></div>
    </div>

    <div @class(['relative flex flex-1 flex-col', 'p-8' => ! $compact, 'px-6 py-5' => $compact])>
        @if ($brand->country)
            <p class="flex items-center gap-2 font-accent italic text-[10px] uppercase tracking-[0.3em] text-text-mint/60">
                <x-country-flag :country="$brand->country" />
                {{ $brand->country }}
            </p>
        @endif
        <h3 @class(['mt-3 font-heading leading-tight text-white', 'text-[28px]' => ! $compact, 'text-[22px]' => $compact])>{{ $brand->name }}</h3>
        <span class="mt-3 block h-px w-10 bg-accent-gold" aria-hidden="true"></span>
        @if ($brand->founded_year)
            <p class="mt-3 font-accent italic text-[13px] text-accent-gold">Est. {{ $brand->founded_year }}</p>
        @endif
        <p class="mt-1 text-xs text-text-mint/70">
            {{ $count === 0 ? 'Sourced on request' : trans_choice(':count piece in the vault|:count pieces in the vault', $count) }}
        </p>

        <span class="maison-card__cta absolute bottom-6 right-8 text-[11px] uppercase tracking-[0.3em] text-accent-gold" aria-hidden="true">Discover →</span>
    </div>
</a>
