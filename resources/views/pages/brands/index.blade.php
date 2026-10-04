{{--
    Photography (Unsplash, placeholder until the maison's own shoot):
      Hero       — photo-1509048191080-d2984bad6ae5 (toned sepia here)
      Invitation — photo-1612817159949-195b6eb9e31a
    Maison of the month uses config('maisons.{slug}.hero_image') or the house's own piece.
--}}
@php
    $heroImage = 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=2000&q=90';
    $inviteImage = 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=2000&q=85';
    $reveal = 'x-data x-intersect.once.margin.-60px="$el.classList.add(\'is-visible\')"';

    $count = $brands->count();
    $numberWords = [1 => 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve'];
    $countWord = $numberWords[$count] ?? number_format($count);

    // Orders for the client-side sort pills (brand id => position).
    $orders = [
        'name' => $brands->sortBy('name')->pluck('id')->flip(),
        'founded' => $brands->sortBy(fn ($b) => $b->founded_year ?? 9999)->pluck('id')->flip(),
        'country' => $brands->sortBy(fn ($b) => ($b->country ?? 'zzz').' '.$b->name)->pluck('id')->flip(),
        'pieces' => $brands->sortBy([['watches_count', 'desc'], ['name', 'asc']])->pluck('id')->flip(),
    ];

    // First house for each letter, by name, for the A–Z jump links.
    $firstByLetter = $brands->sortBy('name')->groupBy(fn ($b) => mb_strtoupper(mb_substr($b->name, 0, 1)))->map->first();

    $featuredMaison = $featured?->maison();
    $featuredImage = $featuredMaison['hero_image'] ?? null;
    $compactPrice = fn (?float $value) => $value === null ? '—' : '€'.($value >= 1000 ? round($value / 1000).'k' : number_format($value, 0));
@endphp

<x-app-layout
    :transparent-nav="true"
    seo-title="The Maisons · ÉLITE"
    seo-description="{{ $countWord }} of the world's great watchmaking houses, curated under one atelier. Explore the registry."
    :seo-image="$heroImage"
>
    @push('head')
        <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
        <style>html { scroll-behavior: smooth; }</style>
    @endpush

    {{-- ═══════════════ 1 · HERO ═══════════════ --}}
    <section class="relative flex min-h-[70vh] items-end overflow-hidden bg-primary-dark" aria-labelledby="maisons-title">
        <img src="{{ $heroImage }}" alt="" class="maison-zoom absolute inset-0 h-full w-full object-cover [filter:sepia(0.55)_saturate(0.7)_contrast(1.05)]" fetchpriority="high" decoding="async">
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(44,53,49,0.5)_0%,rgba(44,53,49,0.95)_100%)]"></div>
        <div class="grain-overlay"></div>

        <div class="relative z-10 mx-auto flex w-full max-w-[1200px] flex-col items-center px-6 pb-[clamp(72px,10vh,100px)] pt-40 text-center">
            <p class="font-accent italic text-sm uppercase tracking-[0.5em] text-accent-gold">The Maisons</p>
            <h1 id="maisons-title" class="mt-6 max-w-[900px] font-heading text-[clamp(48px,7vw,88px)] leading-[1.05] text-white">The great houses, under one roof.</h1>
            <p class="mt-6 max-w-[580px] font-accent italic text-xl text-text-mint/85">
                {{ $countWord }} manufactures.@if ($yearsOfHorology) {{ $yearsOfHorology }} years of horology.@endif One atelier.
            </p>
            <p class="mt-8 text-[13px] uppercase tracking-[0.3em] text-accent-gold">
                {{ $count }} {{ str('maison')->plural($count) }} · {{ $totalPieces }} {{ str('piece')->plural($totalPieces) }} curated
            </p>
        </div>
    </section>

    {{-- ═══════════════ 2 · INTRO ═══════════════ --}}
    <section class="bg-primary-dark px-6 py-[clamp(88px,10vw,120px)]">
        <div class="mx-auto max-w-[760px]">
            <div class="maison-divider" aria-hidden="true"><span></span></div>
            <div class="maison-reveal mt-12 space-y-7 text-[17px] leading-[1.9] text-text-mint" {!! $reveal !!}>
                <p>
                    ÉLITE curates pieces from the manufactures whose names form the vocabulary of horology — the houses that
                    gave the world the waterproof case, the integrated bracelet, the chronograph worn on the Moon. Each one
                    speaks its own dialect of time, and we have learned to listen closely.
                </p>
                <p>
                    Every brand we represent has been chosen for its contribution to the craft — not for marketing.
                    Every piece passes through our atelier before it finds its next owner.
                </p>
            </div>
        </div>
    </section>

    {{-- ═══════════════ 3 · MAISON OF THE MONTH ═══════════════ --}}
    @if ($featured)
        <section class="grid grid-cols-1 bg-primary-dark md:grid-cols-2" aria-labelledby="featured-maison-title">
            <div class="media-overlay relative min-h-[420px] overflow-hidden bg-surface-ink md:min-h-[700px]">
                @if ($featuredImage)
                    <img src="{{ $featuredImage }}" alt="{{ $featured->name }} watch, close up" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async">
                @else
                    <div class="absolute inset-0" style="background: radial-gradient(ellipse at 50% 45%, rgba({{ $featuredMaison['accent'] }}, 0.55) 0%, transparent 70%);" aria-hidden="true"></div>
                    @if ($featured->card_image)
                        <img src="{{ $featured->card_image }}" alt="{{ $featured->name }}" class="absolute inset-0 h-full w-full object-contain p-16 drop-shadow-[0_30px_50px_rgba(0,0,0,0.6)]" loading="lazy" decoding="async">
                    @endif
                @endif
            </div>

            <div class="maison-reveal flex flex-col justify-center px-6 py-16 sm:px-12 lg:p-20" {!! $reveal !!}>
                <p class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">Maison of the month</p>
                <h2 id="featured-maison-title" class="mt-4 font-heading text-[clamp(40px,4.4vw,56px)] leading-none text-white">{{ $featured->name }}</h2>
                <p class="mt-4 font-accent italic text-[15px] text-text-mint/70">
                    {{ collect([$featuredMaison['city'] ?? $featured->country, $featured->founded_year ? 'Founded '.$featured->founded_year : null])->filter()->join(' · ') }}
                </p>
                <div class="maison-divider mt-8 justify-start" aria-hidden="true"><span></span></div>
                <div class="mt-8 space-y-4 text-[15px] leading-[1.9] text-text-mint">
                    @foreach (array_slice($featured->storyParagraphs(), 0, 2) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <dl class="mt-10 grid grid-cols-3 gap-4 border-y border-accent-gold/15 py-6">
                    <div>
                        <dt class="font-accent italic text-[10px] uppercase tracking-[0.25em] text-accent-gold">In the vault</dt>
                        <dd class="mt-2 font-heading text-2xl text-white">{{ $featured->watches_count }}</dd>
                    </div>
                    <div>
                        <dt class="font-accent italic text-[10px] uppercase tracking-[0.25em] text-accent-gold">Range</dt>
                        <dd class="mt-2 font-heading text-2xl text-white">{{ $compactPrice($featured->price_min) }}<span class="text-text-mint/50">–</span>{{ $compactPrice($featured->price_max) }}</dd>
                    </div>
                    <div>
                        <dt class="font-accent italic text-[10px] uppercase tracking-[0.25em] text-accent-gold">Established</dt>
                        <dd class="mt-2 font-heading text-2xl text-white">{{ $featured->founded_year ?? '—' }}</dd>
                    </div>
                </dl>

                <a href="{{ route('brands.show', $featured->slug) }}" class="mt-10 inline-flex min-h-[52px] items-center justify-center self-start border border-accent-gold px-8 py-4 text-xs uppercase tracking-[0.3em] text-accent-gold transition duration-300 hover:bg-accent-gold hover:text-primary-dark">
                    Discover {{ $featured->name }} <span class="ml-3" aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    @endif

    {{-- ═══════════════ 4 · THE FULL REGISTRY ═══════════════ --}}
    <section
        id="registry"
        class="scroll-mt-20 bg-surface-ink bg-[radial-gradient(ellipse_at_top,rgba(17,100,102,0.1)_0%,transparent_55%)] px-6 py-[clamp(96px,11vw,140px)]"
        aria-labelledby="registry-title"
        x-data="{ sort: 'name', letter: null, orders: {{ Js::from($orders) }} }"
    >
        <div class="mx-auto max-w-[1200px]">
            <div class="maison-reveal text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">The full registry</p>
                <h2 id="registry-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">Each house. A different language of time.</h2>
            </div>

            <div class="mt-14 flex flex-col gap-5 border-y border-accent-gold/10 py-5">
                <nav class="flex flex-wrap items-center justify-between gap-y-1" aria-label="Jump to a maison by letter">
                    @foreach (range('A', 'Z') as $char)
                        @if ($firstByLetter->has($char))
                            <a href="#maison-{{ $firstByLetter[$char]->slug }}" class="registry-letter" x-bind:class="letter === '{{ $char }}' && 'is-active'" x-on:click="letter = '{{ $char }}'">{{ $char }}</a>
                        @else
                            <span class="registry-letter" aria-hidden="true">{{ $char }}</span>
                        @endif
                    @endforeach
                </nav>

                <div class="flex flex-wrap items-center gap-2 border-t border-accent-gold/10 pt-5" role="group" aria-label="Sort the registry">
                    <span class="mr-2 font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold/70">Sort</span>
                    @foreach (['name' => 'By name', 'founded' => 'By founded year', 'country' => 'By country', 'pieces' => 'By pieces available'] as $key => $label)
                        <button type="button" class="registry-pill" x-on:click="sort = '{{ $key }}'" x-bind:aria-pressed="(sort === '{{ $key }}').toString()" aria-pressed="{{ $key === 'name' ? 'true' : 'false' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <ul class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($brands as $brand)
                    <li
                        id="maison-{{ $brand->slug }}"
                        class="scroll-mt-28"
                        style="order: {{ $orders['name'][$brand->id] }}"
                        x-bind:style="'order: ' + orders[sort][{{ $brand->id }}]"
                    >
                        <x-brand-card :brand="$brand" />
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══════════════ 5 · ON SELECTION ═══════════════ --}}
    <section class="bg-primary-dark px-6 py-[clamp(88px,10vw,120px)]" aria-labelledby="selection-title">
        <div class="maison-reveal mx-auto max-w-[760px] text-center" {!! $reveal !!}>
            <div class="maison-divider" aria-hidden="true"><span></span></div>
            <p class="mt-10 font-accent italic text-xs uppercase tracking-[0.5em] text-accent-gold">On selection</p>
            <h2 id="selection-title" class="mt-5 font-heading text-[clamp(28px,3vw,36px)] text-white">We do not represent every brand.</h2>
            <p class="mt-6 text-[15px] leading-[1.9] text-text-mint">
                A maison enters our catalog only when its craft, its heritage, and its integrity meet the standard we hold ourselves to.
                We decline more than we accept. The list grows slowly, and with intention.
            </p>
            <p class="mt-6 font-accent italic text-[13px] uppercase tracking-[0.25em] text-accent-gold">— The Atelier</p>
        </div>
    </section>

    {{-- ═══════════════ 6 · INVITATION ═══════════════ --}}
    <section class="relative flex min-h-[50vh] items-center overflow-hidden px-6 py-24" aria-labelledby="unlisted-maison-title">
        <img src="{{ $inviteImage }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(44,53,49,0.9)_0%,rgba(17,100,102,0.7)_100%)]"></div>
        <div class="maison-reveal relative mx-auto flex max-w-[760px] flex-col items-center text-center" {!! $reveal !!}>
            <span class="h-px w-20 bg-accent-gold" aria-hidden="true"></span>
            <p class="mt-8 font-accent italic text-[13px] uppercase tracking-[0.5em] text-accent-gold">Do not see your maison?</p>
            <h2 id="unlisted-maison-title" class="mt-6 font-heading text-[clamp(32px,4vw,48px)] leading-[1.12] text-white">Our atelier may still source it.</h2>
            <a href="{{ route('contact.index', ['subject' => 'unlisted']) }}#written-request" class="mt-10 inline-flex min-h-[52px] items-center justify-center border border-accent-gold bg-accent-gold px-9 py-[18px] text-xs uppercase tracking-[0.3em] text-primary-dark transition duration-300 hover:border-primary-teal hover:bg-primary-teal hover:text-white">
                Request a piece
            </a>
        </div>
    </section>
</x-app-layout>
