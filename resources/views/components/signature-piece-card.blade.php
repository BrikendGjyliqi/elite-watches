{{-- One of a maison's "three to know". --}}
@props(['watch', 'brand'])

@php
    $image = \App\Support\WatchImageUrl::primary($watch);
    $line = $watch->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $watch->description), 110);
    $price = (float) ($watch->discount_price ?? $watch->price);
@endphp

<a href="{{ route('shop.show', $watch->slug) }}" {{ $attributes->class(['signature-card group flex h-full flex-col border border-accent-gold/40 bg-surface-card']) }}>
    <div class="relative aspect-[4/5] overflow-hidden border-b border-accent-gold/30">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_45%,rgba(217,176,141,0.16)_0%,transparent_62%)]" aria-hidden="true"></div>
        @if ($image)
            <img src="{{ $image }}" alt="{{ $watch->name }}" class="signature-card__image absolute inset-0 h-full w-full object-contain p-10 drop-shadow-[0_24px_36px_rgba(0,0,0,0.6)]" loading="lazy" decoding="async">
        @endif
    </div>

    <div class="flex flex-1 flex-col p-8">
        <p class="font-accent italic text-[10px] uppercase tracking-[0.3em] text-accent-gold">{{ $brand->name }}</p>
        <h3 class="mt-2 font-heading text-[22px] leading-snug text-white">{{ $watch->name }}</h3>
        @if ($watch->reference_number)
            <p class="mt-1 font-mono text-[11px] text-text-mint/60">Ref. {{ $watch->reference_number }}</p>
        @endif
        @if ($line)
            <p class="mt-4 font-accent italic text-[15px] leading-relaxed text-text-mint/80">{{ $line }}</p>
        @endif
        <p class="mt-auto pt-6 font-heading text-[22px] text-accent-gold">€{{ number_format($price, 0, ',', '.') }}</p>
        <span class="mt-5 block h-px w-full bg-accent-gold/20" aria-hidden="true"></span>
        <span class="mt-5 text-[11px] uppercase tracking-[0.3em] text-accent-gold transition group-hover:text-accent-peach">View piece <span class="inline-block transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></span>
    </div>
</a>
