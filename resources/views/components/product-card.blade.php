@props(['watch'])

@php
    $primaryImage = $watch->images->firstWhere('is_primary', true) ?? $watch->images->first();
    $hasDiscount = ! is_null($watch->discount_price);
@endphp

<div
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
    class="group relative transition-all duration-700 ease-out"
>
    <a
        href="{{ route('shop.show', $watch->slug) }}"
        class="block rounded-lg border border-transparent hover:border-accent-gold/70 bg-primary-dark/40 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-black/40"
    >
        <div class="relative aspect-square overflow-hidden rounded-lg media-overlay bg-primary-teal/10">
            @if ($primaryImage)
                <img
                    src="{{ $primaryImage->path }}"
                    alt="{{ $watch->name }}"
                    loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                >
            @endif

            <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
                @if ($watch->is_new)
                    <span class="px-2.5 py-1 bg-primary-dark/80 text-accent-peach text-[10px] uppercase tracking-widest">{{ __('New') }}</span>
                @endif
                @if ($hasDiscount)
                    <span class="px-2.5 py-1 bg-accent-gold text-primary-dark text-[10px] uppercase tracking-widest font-semibold">{{ __('Sale') }}</span>
                @endif
            </div>
        </div>

        <div class="pt-5 pb-2 text-center">
            <p class="text-[11px] uppercase tracking-[0.2em] text-accent-gold/80">
                {{ $watch->brand->name }}
            </p>

            <h3 class="mt-2 font-heading text-lg text-white group-hover:text-accent-peach transition-colors">
                {{ $watch->name }}
            </h3>

            <div class="mt-3 flex items-center justify-center gap-2">
                @if ($hasDiscount)
                    <span class="text-sm text-text-mint/50 line-through">€{{ number_format((float) $watch->price, 0, ',', '.') }}</span>
                    <span class="font-heading text-accent-gold text-lg">€{{ number_format((float) $watch->discount_price, 0, ',', '.') }}</span>
                @else
                    <span class="font-heading text-accent-gold text-lg">€{{ number_format((float) $watch->price, 0, ',', '.') }}</span>
                @endif
            </div>

            <span class="mt-4 inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.2em] text-text-mint group-hover:text-accent-gold transition-colors">
                {{ __('View') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </span>
        </div>
    </a>
</div>
