{{--
    A maison's chapter. Editorial details come from config/maisons.php (falling back to
    defaults); the hero uses config('maisons.{slug}.hero_image') when set, otherwise the
    house's own piece floating over its accent colour.
    Contact band photography: Unsplash photo-1547996160-81dfa63595aa.
--}}
@php
    $reveal = 'x-data x-intersect.once.margin.-60px="$el.classList.add(\'is-visible\')"';
    $accent = $maison['accent'];
    $heroPhoto = $maison['hero_image'] ?? null;
    $contactImage = 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?w=2000&q=85';

    $originLine = collect([$maison['city'] ?? null, $brand->country, $brand->founded_year ? 'Established '.$brand->founded_year : null])
        ->filter()->unique()->join(' · ');

    $yearsSince = $brand->founded_year ? now()->year - $brand->founded_year : null;
    $stats = array_values(array_filter([
        $brand->founded_year ? ['value' => (string) $brand->founded_year, 'label' => 'Year founded', 'note' => $maison['founder'] ?? $brand->country] : null,
        ! empty($maison['signature']) ? ['value' => $maison['signature']['year'], 'label' => $maison['signature']['name'], 'note' => $maison['signature']['note']] : null,
        $yearsSince ? ['value' => (string) $yearsSince, 'label' => 'Years of horology', 'note' => 'Since '.$brand->founded_year] : null,
        ['value' => (string) $brand->watches_count, 'label' => 'In the vault', 'note' => 'Currently with ÉLITE'],
    ]));

    // The pull quote sits in the middle of the story.
    $quoteAfter = max(1, intdiv(count($story), 2));
@endphp

<x-app-layout
    :transparent-nav="true"
    seo-title="{{ $brand->name }} · ÉLITE Maison Horlogère"
    seo-description="Discover {{ $brand->name }} pieces curated by the ÉLITE atelier.{{ $brand->founded_year ? ' '.$brand->founded_year : '' }}{{ $brand->country ? ' · '.$brand->country : '' }}."
    :seo-image="$heroPhoto ?? $heroWatchImage"
>
    @push('head')
        @if ($heroPhoto ?? $heroWatchImage)
            <link rel="preload" as="image" href="{{ $heroPhoto ?? $heroWatchImage }}" fetchpriority="high">
        @endif
        <style>html { scroll-behavior: smooth; }</style>
    @endpush

    {{-- ═══════════════ 1 · HERO ═══════════════ --}}
    <section class="relative flex h-screen min-h-[640px] items-end overflow-hidden bg-surface-ink" aria-labelledby="maison-name">
        @if ($heroPhoto)
            <img src="{{ $heroPhoto }}" alt="" class="maison-zoom absolute inset-0 h-full w-full object-cover" fetchpriority="high" decoding="async">
        @else
            {{-- The house's piece, soft and enlarged behind, crisp and floating in front --}}
            <div class="absolute inset-0" style="background: radial-gradient(ellipse at 50% 38%, rgba({{ $accent }}, 0.7) 0%, rgba({{ $accent }}, 0.18) 38%, transparent 72%);" aria-hidden="true"></div>
            @if ($heroWatchImage)
                <img src="{{ $heroWatchImage }}" alt="" class="absolute inset-0 h-full w-full scale-150 object-contain opacity-20 blur-3xl" aria-hidden="true" decoding="async">
                {{-- Centred by flex: the float animation owns the image's transform --}}
                <div class="absolute inset-x-0 top-[14%] flex justify-center">
                    <img src="{{ $heroWatchImage }}" alt="{{ $brand->name }}" class="maison-hero-piece h-[44vh] max-h-[460px] w-auto object-contain drop-shadow-[0_40px_60px_rgba(0,0,0,0.65)]" fetchpriority="high" decoding="async">
                </div>
            @endif
        @endif
        <div class="absolute inset-0" style="background: linear-gradient(180deg, transparent 0%, rgba(44,53,49,{{ $heroPhoto ? '0.55' : '0.25' }}) 45%, rgba(44,53,49,0.94) 100%);" aria-hidden="true"></div>
        <div class="grain-overlay"></div>

        <div class="relative z-10 mx-auto flex w-full max-w-[1200px] flex-col items-center px-6 pb-[clamp(72px,11vh,110px)] text-center">
            @if ($originLine)
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold [text-shadow:0_1px_14px_rgba(0,0,0,0.9)]">{{ $originLine }}</p>
            @endif
            <h1 id="maison-name" class="mt-5 font-heading text-[clamp(64px,10vw,140px)] leading-none tracking-[0.04em] text-white">{{ $brand->name }}</h1>
            <span class="mt-8 block h-px w-[120px] bg-accent-gold" aria-hidden="true"></span>
            <p class="mt-8 max-w-[700px] font-accent italic text-xl text-text-mint/85">&ldquo;{{ $maison['tagline'] }}&rdquo;</p>
        </div>

        <a href="#story" class="absolute bottom-6 left-1/2 z-10 -translate-x-1/2 p-3" aria-label="Scroll to the story of {{ $brand->name }}">
            <span class="maison-scroll-cue block"></span>
        </a>
    </section>

    {{-- ═══════════════ 2 · THE STORY ═══════════════ --}}
    <section id="story" class="scroll-mt-20 bg-primary-dark px-6 py-[clamp(96px,11vw,140px)]" aria-labelledby="story-title">
        <div class="mx-auto max-w-[820px]">
            <div class="maison-reveal" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">The story</p>
                <h2 id="story-title" class="mt-5 font-heading text-[clamp(28px,3vw,36px)] leading-snug text-white">{{ $brand->name }} · {{ $maison['framing'] }}</h2>
            </div>

            <div class="mt-12 space-y-7 text-[17px] leading-[1.9] text-text-mint">
                @foreach ($story as $paragraph)
                    <p class="maison-reveal" {!! $reveal !!}>{{ $paragraph }}</p>

                    @if ($loop->iteration === $quoteAfter)
                        <figure class="maison-reveal relative !my-14 py-2 pl-8" {!! $reveal !!}>
                            <span class="absolute bottom-0 left-0 top-0 w-[3px] bg-accent-gold" aria-hidden="true"></span>
                            <blockquote class="font-heading text-[clamp(22px,2.4vw,28px)] italic leading-snug text-white">&ldquo;A watch is not an accessory. It is a statement of priorities.&rdquo;</blockquote>
                            <figcaption class="mt-4 font-accent italic text-[13px] uppercase tracking-[0.25em] text-accent-gold">— Anonymous collector</figcaption>
                        </figure>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ 3 · SIGNATURE PIECES ═══════════════ --}}
    @if ($signaturePieces->isNotEmpty())
        <section class="bg-surface-ink px-6 py-[clamp(88px,10vw,120px)]" aria-labelledby="signature-title">
            <div class="mx-auto max-w-[1200px]">
                <div class="maison-reveal text-center" {!! $reveal !!}>
                    <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">Signature pieces</p>
                    <h2 id="signature-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">
                        {{ ['', 'One to know.', 'Two to know.', 'Three to know.'][$signaturePieces->count()] }}
                    </h2>
                </div>

                <div @class([
                    'mx-auto mt-16 grid grid-cols-1 gap-6',
                    'md:grid-cols-3' => $signaturePieces->count() === 3,
                    'max-w-[820px] md:grid-cols-2' => $signaturePieces->count() === 2,
                    'max-w-[420px]' => $signaturePieces->count() === 1,
                ])>
                    @foreach ($signaturePieces as $watch)
                        <div class="maison-reveal" style="transition-delay: {{ $loop->index * 100 }}ms" {!! $reveal !!}>
                            <x-signature-piece-card :watch="$watch" :brand="$brand" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-14 text-center">
                    <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}" class="inline-flex min-h-[52px] items-center justify-center border border-accent-gold px-8 py-4 text-xs uppercase tracking-[0.3em] text-accent-gold transition duration-300 hover:bg-accent-gold hover:text-primary-dark">
                        View all {{ $brand->watches_count }} {{ str('piece')->plural($brand->watches_count) }} by {{ $brand->name }} <span class="ml-3" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════ 4 · HERITAGE ═══════════════ --}}
    <section class="bg-primary-dark px-6 py-[clamp(80px,9vw,100px)]" aria-labelledby="heritage-numbers-title">
        <div class="mx-auto max-w-[1200px]">
            <p id="heritage-numbers-title" class="text-center font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">Heritage</p>
            <dl class="mt-12 grid grid-cols-2 gap-px bg-accent-gold/10 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="maison-reveal bg-primary-dark px-4 py-10 text-center" {!! $reveal !!}>
                        <dd class="font-heading text-[clamp(40px,4.6vw,56px)] leading-none text-accent-gold tabular-nums">{{ $stat['value'] }}</dd>
                        <dt class="mt-4 font-accent italic text-[10px] uppercase tracking-[0.3em] text-accent-gold">{{ $stat['label'] }}</dt>
                        <p class="mt-2 text-xs text-text-mint/60">{{ $stat['note'] }}</p>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ═══════════════ 5 · THE FULL COLLECTION ═══════════════ --}}
    <section id="collection" class="scroll-mt-20 bg-primary-dark px-6 pb-[clamp(96px,11vw,140px)] pt-10" aria-labelledby="collection-title">
        <div class="mx-auto max-w-[1200px]">
            <div class="maison-divider" aria-hidden="true"><span></span></div>
            <div class="maison-reveal mt-12 text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">The full collection</p>
                <h2 id="collection-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">Every piece currently in the vault.</h2>
            </div>

            @if ($brand->watches_count > 0)
                <form method="GET" action="{{ route('brands.show', $brand->slug) }}#collection" class="mt-12 flex flex-col gap-4 border-y border-accent-gold/10 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs uppercase tracking-[0.2em] text-text-mint/60">
                        <span class="text-text-mint">{{ $watches->total() }}</span> {{ str('piece')->plural($watches->total()) }}
                        @if ($priceRange)
                            <span class="mx-2 text-accent-gold/40">·</span>€{{ number_format($priceRange['min'], 0, ',', '.') }} – €{{ number_format($priceRange['max'], 0, ',', '.') }}
                        @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        @if ($categories->count() > 1)
                            <label class="sr-only" for="collection-category">Category</label>
                            <select id="collection-category" name="category" onchange="this.form.submit()" class="concierge-field w-auto min-w-[11rem] !py-2 text-sm">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) ($filters['category'] ?? 0) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <label class="sr-only" for="collection-sort">Sort</label>
                        <select id="collection-sort" name="sort" onchange="this.form.submit()" class="concierge-field w-auto min-w-[11rem] !py-2 text-sm">
                            <option value="curated" @selected(($filters['sort'] ?? 'curated') === 'curated')>Curated order</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: high to low</option>
                        </select>
                        <noscript><button type="submit" class="registry-pill">Apply</button></noscript>
                    </div>
                </form>

                <div class="maison-collection mt-12 grid grid-cols-1 gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($watches as $watch)
                        <x-product-card :watch="$watch" />
                    @endforeach
                </div>

                @if ($watches->hasPages())
                    <div class="mt-16">{{ $watches->links() }}</div>
                @endif
            @else
                <p class="mt-12 text-center font-accent italic text-lg text-text-mint/60">No {{ $brand->name }} pieces are in the vault just now — our atelier can source one for you.</p>
            @endif
        </div>
    </section>

    {{-- ═══════════════ 6 · KINDRED HOUSES ═══════════════ --}}
    @if ($related->isNotEmpty())
        <section class="bg-surface-ink py-[clamp(80px,9vw,100px)]" aria-labelledby="kindred-title">
            <div class="mx-auto max-w-[1200px] px-6">
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">Kindred houses</p>
                <h2 id="kindred-title" class="mt-4 font-heading text-[clamp(24px,2.6vw,32px)] text-white">Collectors of {{ $brand->name }} also acquire from:</h2>
            </div>
            <ul class="kindred-strip mx-auto mt-10 flex max-w-[1200px] gap-5 overflow-x-auto px-6 pb-4">
                @foreach ($related as $kindred)
                    <li class="w-[260px] shrink-0">
                        <x-brand-card :brand="$kindred" compact />
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ═══════════════ 7 · CONTACT ═══════════════ --}}
    <section class="relative flex min-h-[40vh] items-center overflow-hidden px-6 py-24" aria-labelledby="source-title">
        <img src="{{ $contactImage }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(44,53,49,0.92) 0%, rgba({{ $accent }}, 0.7) 100%);" aria-hidden="true"></div>
        <div class="maison-reveal relative mx-auto flex max-w-[700px] flex-col items-center text-center" {!! $reveal !!}>
            <p class="font-accent italic text-[13px] uppercase tracking-[0.4em] text-accent-gold">Interested in a piece not listed?</p>
            <h2 id="source-title" class="mt-5 font-heading text-[clamp(28px,3vw,36px)] text-white">Our atelier can source.</h2>
            <p class="mt-5 text-[15px] leading-[1.9] text-text-mint">
                We maintain relationships with dealers and private collectors across Europe and beyond.
                If you are searching for a specific {{ $brand->name }} reference, write to us.
            </p>
            <a href="{{ route('contact.index', ['subject' => 'unlisted', 'brand' => $brand->name]) }}#written-request" class="mt-10 inline-flex min-h-[52px] items-center justify-center border border-accent-gold bg-accent-gold px-9 py-[18px] text-xs uppercase tracking-[0.3em] text-primary-dark transition duration-300 hover:border-white hover:bg-transparent hover:text-white">
                Request a piece
            </a>
        </div>
    </section>
</x-app-layout>
