{{--
    Photography (Unsplash, placeholder until the maison's own editorial shoot):
      Hero      — photo-1587836374828-4dbafa94cf0e
      The Craft — photo-1509048191080-d2984bad6ae5
      CTA band  — photo-1612817159949-195b6eb9e31a (the brief's photo-1548181048 no longer exists on Unsplash)
    https://unsplash.com/license
--}}
@php
    $heroImage = 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=2000&q=90';
    $craftImage = 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=1600&q=90';
    $inviteImage = 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=2000&q=90';

    // Editorial content — kept together so the maison's facts are easy to keep current.
    $milestones = [
        ['year' => '2024', 'title' => 'A spark in Prishtina', 'body' => 'Brikend begins trading his first vintage pieces among friends and collectors across the Balkans.'],
        ['year' => '2025', 'title' => 'The first commission', 'body' => 'A client in Zurich requests a Patek Philippe Aquanaut. The maison fulfills its first six-figure acquisition.'],
        ['year' => '2026', 'title' => 'ÉLITE is founded', 'body' => 'The maison is formally established. The atelier opens its private portal.'],
        ['year' => '2026', 'title' => 'The circle grows', 'body' => 'Partnerships with authorized dealers in Switzerland, Italy, and the UAE expand our catalog to over 200 pieces.'],
        ['year' => null, 'title' => 'What comes next', 'body' => 'A physical boutique in central Prishtina. Our first in-house complication. The story is still being written.'],
    ];

    $figures = [
        ['value' => 217, 'decimals' => 0, 'prefix' => '', 'suffix' => '', 'label' => 'Pieces curated', 'note' => 'Across 23 maisons'],
        ['value' => 4.8, 'decimals' => 1, 'prefix' => '€', 'suffix' => 'M', 'label' => 'Acquired through the atelier', 'note' => 'Since inception'],
        ['value' => 48, 'decimals' => 0, 'prefix' => '', 'suffix' => 'h', 'label' => 'Average response time', 'note' => 'From request to confirmation'],
        ['value' => 100, 'decimals' => 0, 'prefix' => '', 'suffix' => '%', 'label' => 'Authenticated', 'note' => 'Every piece. No exceptions.'],
    ];

    $principles = [
        ['numeral' => 'I', 'title' => 'Discretion', 'line' => 'Your request, your piece, your story — kept inside the maison.'],
        ['numeral' => 'II', 'title' => 'Patience', 'line' => 'We do not rush an acquisition. The right piece arrives at the right moment.'],
        ['numeral' => 'III', 'title' => 'Permanence', 'line' => 'A watch chosen with care becomes an heirloom. We help you choose.'],
    ];

    // Shared reveal behaviour: fade up once, the first time a block enters the viewport.
    $reveal = 'x-data x-intersect.once.margin.-60px="$el.classList.add(\'is-visible\')"';
@endphp

<x-app-layout
    :transparent-nav="true"
    seo-title="About · ÉLITE Maison Horlogère"
    seo-description="The story of ÉLITE — a Prishtina-based maison dedicated to the acquisition and curation of the world's finest timepieces."
    og-description="Time, held in trust."
    :seo-image="$heroImage"
>
    @push('head')
        <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
        <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
        <style>html { scroll-behavior: smooth; }</style>
    @endpush

    {{-- ═══════════════ 1 · HERO ═══════════════ --}}
    <section class="relative h-screen min-h-[640px] overflow-hidden bg-primary-dark" aria-labelledby="maison-hero-title">
        <img
            src="{{ $heroImage }}"
            alt="Macro photograph of a steel watch dial on black"
            class="maison-zoom absolute inset-0 h-full w-full object-cover"
            fetchpriority="high"
            decoding="async"
        >
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(44,53,49,0.4)_0%,rgba(44,53,49,0.9)_100%)]"></div>
        <div class="grain-overlay"></div>

        <div class="relative z-10 flex h-full flex-col items-center justify-end px-6 pb-[120px] text-center">
            <p class="font-accent italic text-sm uppercase tracking-[0.5em] text-accent-gold">The Maison</p>
            <h1 id="maison-hero-title" class="mt-6 max-w-[900px] font-heading text-[clamp(48px,7vw,96px)] leading-[1.05] text-white">
                Time, held in trust.
            </h1>
            <p class="mt-6 max-w-[600px] font-accent italic text-xl text-text-mint/85">
                The story of a maison dedicated to the finest horology of our time.
            </p>
        </div>

        <a href="#letter" class="absolute bottom-8 left-1/2 z-10 -translate-x-1/2 p-3" aria-label="Scroll to the founder's letter">
            <span class="maison-scroll-cue block"></span>
        </a>
    </section>

    {{-- ═══════════════ 2 · THE OPENING LETTER ═══════════════ --}}
    <section id="letter" class="scroll-mt-20 bg-primary-dark px-6 py-[clamp(96px,12vw,160px)]">
        <div class="mx-auto max-w-[760px]">
            <div class="maison-divider" aria-hidden="true"><span></span></div>

            <div class="maison-reveal mt-12 text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">A letter from the founder</p>
                <blockquote class="mt-8 font-heading italic text-[clamp(26px,3.6vw,36px)] leading-[1.3] text-white">
                    &ldquo;We did not set out to sell watches. We set out to earn the right to be remembered.&rdquo;
                </blockquote>
            </div>

            <div class="maison-reveal mt-14 space-y-7 text-[17px] leading-[1.9] text-text-mint" {!! $reveal !!}>
                <p>
                    The world is not short of places to buy a watch. It is short of places that understand why you want one.
                    ÉLITE began with a simple discomfort: that the pieces we admired most were being sold like any other
                    product — quickly, impersonally, and without a thought for the life they would go on to live.
                </p>
                <p>
                    So we built a maison around three quiet disciplines. Provenance, because every watch carries a history
                    that deserves to be known. Craftsmanship, because the hands that made it should be honoured by the hands
                    that sell it. And patience, because the right piece is never found in a hurry.
                </p>
                <p>
                    This is not a store. It is an atelier — a small room where collectors are received by name, where
                    every request is read by a person, and where a watch leaves only when it has found the owner it was waiting for.
                    You are welcome here.
                </p>
            </div>

            <div class="maison-reveal mt-16 flex flex-col items-center text-center" {!! $reveal !!}>
                <svg viewBox="0 0 260 70" class="h-[70px] w-[260px] text-accent-gold" role="img" aria-label="Signature of Brikend Gjyliqi">
                    <text x="130" y="44" text-anchor="middle" fill="currentColor" style="font-family: 'Great Vibes', cursive; font-size: 40px;">B. Gjyliqi</text>
                    <path d="M58 56 C 110 50, 170 62, 214 52" fill="none" stroke="currentColor" stroke-width="0.8" stroke-linecap="round" opacity="0.7" />
                </svg>
                <p class="mt-2 font-accent italic text-[13px] text-accent-gold">Brikend Gjyliqi</p>
                <p class="mt-1 text-[11px] uppercase tracking-[0.3em] text-text-mint/60">Fondateur · Prishtina</p>
            </div>
        </div>
    </section>

    {{-- ═══════════════ 3 · HERITAGE TIMELINE ═══════════════ --}}
    <section class="maison-grain relative bg-surface-card px-6 py-[clamp(96px,11vw,140px)]" aria-labelledby="heritage-title">
        <div class="relative mx-auto max-w-[1200px]">
            <div class="maison-reveal text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">Heritage</p>
                <h2 id="heritage-title" class="mx-auto mt-5 max-w-[800px] font-heading text-[clamp(32px,4.2vw,48px)] leading-tight text-white">
                    A short history of our conviction.
                </h2>
            </div>

            <ol class="maison-timeline relative mt-20 grid grid-cols-1 gap-6 border-l border-accent-gold/30 pl-8 md:grid-cols-5 md:gap-5 md:border-l-0 md:pl-0 md:pt-0">
                @foreach ($milestones as $milestone)
                    <li class="maison-reveal relative md:pt-10" style="transition-delay: {{ $loop->index * 90 }}ms" {!! $reveal !!}>
                        {{-- Node on the thread: on the hairline above (desktop) or the rail at left (mobile) --}}
                        <span class="maison-milestone-node absolute -left-[37px] top-8 rotate-45 md:left-1/2 md:top-0 md:-translate-x-1/2 md:-translate-y-1/2" aria-hidden="true"></span>

                        <article class="maison-milestone h-full border border-accent-gold/10 bg-primary-dark/40 p-6 text-left md:text-center">
                            @if ($milestone['year'])
                                <p class="font-heading text-5xl leading-none text-accent-gold">{{ $milestone['year'] }}</p>
                                <h3 class="mt-5 font-heading text-xl tracking-wide text-white md:flex md:min-h-[3.5rem] md:items-start md:justify-center">{{ $milestone['title'] }}</h3>
                            @else
                                {{-- The open chapter: no year, its title takes the year's place in italic gold --}}
                                <h3 class="font-accent text-[clamp(30px,2.6vw,38px)] italic leading-[1.05] text-accent-gold md:flex md:min-h-[calc(3rem+1.25rem+3.5rem)] md:items-start md:justify-center">{{ $milestone['title'] }}</h3>
                            @endif
                            <span class="my-5 block h-10 w-px bg-accent-gold/60 md:mx-auto" aria-hidden="true"></span>
                            <p class="max-w-[240px] text-sm leading-relaxed text-text-mint md:mx-auto">{{ $milestone['body'] }}</p>
                        </article>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ═══════════════ 4 · THE CRAFT ═══════════════ --}}
    <section class="grid grid-cols-1 bg-primary-dark lg:grid-cols-5" aria-labelledby="craft-title">
        <div class="media-overlay relative min-h-[420px] lg:col-span-3 lg:min-h-[720px]">
            <img
                src="{{ $craftImage }}"
                alt="A pocket watch suspended on its chain, photographed in soft daylight"
                class="absolute inset-0 h-full w-full object-cover"
                loading="lazy"
                decoding="async"
            >
        </div>

        <div class="maison-reveal flex flex-col justify-center px-6 py-16 sm:px-12 lg:col-span-2 lg:p-20" {!! $reveal !!}>
            <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">The Craft</p>
            <h2 id="craft-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] leading-tight text-white">Every piece. Verified by hand.</h2>

            <div class="mt-8 space-y-6 text-base leading-[1.9] text-text-mint">
                <p>
                    <span class="text-white">Sourcing.</span>
                    Every watch is acquired directly from the manufacturer, an authorized dealer, or a private collector
                    we have vetted in person. If its origin cannot be documented, it does not enter the maison.
                </p>
                <p>
                    <span class="text-white">Authentication.</span>
                    A certified watchmaker inspects the movement, serial, case, dial, bracelet and papers before a piece is
                    catalogued — opening the caseback where the maker allows it.
                </p>
                <p>
                    <span class="text-white">Presentation.</span>
                    Each piece is photographed, recorded and kept in a climate-controlled vault until its next owner is found.
                </p>
            </div>

            {{-- Verification seal --}}
            <div class="mt-12 flex items-center gap-5">
                <svg viewBox="0 0 120 120" class="h-[104px] w-[104px] shrink-0 text-accent-gold" role="img" aria-label="ÉLITE certified seal">
                    <defs>
                        <path id="maison-seal-path" d="M60,60 m-44,0 a44,44 0 1,1 88,0 a44,44 0 1,1 -88,0" />
                    </defs>
                    <circle cx="60" cy="60" r="57" fill="none" stroke="currentColor" stroke-width="0.8" />
                    <circle cx="60" cy="60" r="34" fill="none" stroke="currentColor" stroke-width="0.6" opacity="0.6" />
                    <g class="maison-seal-ring">
                        <text fill="currentColor" style="font-family: 'Inter', sans-serif; font-size: 9.5px; letter-spacing: 3.2px;">
                            <textPath href="#maison-seal-path">ÉLITE · CERTIFIED · ÉLITE · CERTIFIED ·</textPath>
                        </text>
                    </g>
                    {{-- Crown --}}
                    <path d="M45 70 L42 52 L51 59 L60 47 L69 59 L78 52 L75 70 Z" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linejoin="round" />
                    <line x1="44" y1="75" x2="76" y2="75" stroke="currentColor" stroke-width="1.1" />
                    <circle cx="60" cy="44" r="1.6" fill="currentColor" />
                </svg>
                <p class="font-accent italic text-base leading-snug text-text-mint/70">
                    Each piece leaves the atelier<br>with our certificate of authenticity.
                </p>
            </div>
        </div>
    </section>

    {{-- ═══════════════ 5 · THE NUMBERS ═══════════════ --}}
    <section class="bg-surface-ink px-6 py-[clamp(88px,10vw,120px)]" aria-labelledby="numbers-title">
        <div class="mx-auto max-w-[1200px]">
            <div class="maison-reveal text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">By the numbers</p>
                <h2 id="numbers-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">Trust, measured.</h2>
            </div>

            <dl class="mt-16 grid grid-cols-1 gap-px bg-accent-gold/10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($figures as $figure)
                    <div
                        class="maison-reveal bg-surface-ink px-6 py-10 text-center"
                        x-data="{
                            target: {{ $figure['value'] }},
                            shown: @js($figure['prefix'].number_format($figure['value'], $figure['decimals']).$figure['suffix']),
                            run() {
                                $el.classList.add('is-visible');
                                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                                const start = performance.now();
                                const step = (now) => {
                                    const p = Math.min((now - start) / 1200, 1);
                                    const eased = 1 - Math.pow(1 - p, 3);
                                    this.shown = @js($figure['prefix']) + (this.target * eased).toFixed({{ $figure['decimals'] }}) + @js($figure['suffix']);
                                    if (p < 1) requestAnimationFrame(step);
                                };
                                requestAnimationFrame(step);
                            },
                        }"
                        x-intersect.once.margin.-60px="run()"
                    >
                        <dd class="font-heading text-[clamp(56px,6vw,72px)] leading-none text-accent-gold tabular-nums" x-text="shown">
                            {{ $figure['prefix'].number_format($figure['value'], $figure['decimals']).$figure['suffix'] }}
                        </dd>
                        <dt class="mt-5 font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">{{ $figure['label'] }}</dt>
                        <p class="mt-2 text-[13px] text-text-mint/70">{{ $figure['note'] }}</p>
                    </div>
                @endforeach
            </dl>

            <div class="mt-14 flex flex-col items-center gap-5">
                <span class="h-px w-20 bg-accent-gold/60" aria-hidden="true"></span>
                <p class="font-accent italic text-sm text-text-mint/60">As of October 2026 · Audited internally</p>
            </div>
        </div>
    </section>

    {{-- ═══════════════ 6 · THE ATELIER ═══════════════ --}}
    <section class="bg-primary-dark px-6 py-[clamp(96px,11vw,140px)]" aria-labelledby="atelier-title">
        <div class="mx-auto max-w-[900px] text-center">
            <div class="maison-divider" aria-hidden="true"><span></span></div>

            <div class="maison-reveal mt-12" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">The Atelier</p>
                <h2 id="atelier-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">Not a store. A quiet room.</h2>
            </div>

            <div class="maison-reveal mx-auto mt-10 max-w-[760px] space-y-7 text-[17px] leading-[1.9] text-text-mint" {!! $reveal !!}>
                <p>
                    ÉLITE works by appointment and by acquisition request, never by instant checkout. When you ask for a piece,
                    a member of the atelier reads your request, confirms the watch, and oversees every step until it is in your hands.
                </p>
                <p>
                    Serious collectors are invited to open a private account, to ask for pieces we have not listed, or to be told
                    first when something rare is about to arrive.
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-5 text-left md:grid-cols-3">
                @foreach ($principles as $principle)
                    <article class="maison-reveal border border-accent-gold/15 bg-surface-card/60 p-8" style="transition-delay: {{ $loop->index * 100 }}ms" {!! $reveal !!}>
                        <p class="font-heading text-[32px] leading-none text-accent-gold/90">{{ $principle['numeral'] }}</p>
                        <h3 class="mt-6 font-heading text-lg text-white">{{ $principle['title'] }}</h3>
                        <p class="mt-2 font-accent italic text-[15px] leading-relaxed text-text-mint">{{ $principle['line'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ 7 · INVITATION ═══════════════ --}}
    <section class="relative flex min-h-[60vh] items-center overflow-hidden px-6 py-28" aria-labelledby="invitation-title">
        <img
            src="{{ $inviteImage }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover"
            loading="lazy"
            decoding="async"
        >
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(44,53,49,0.9)_0%,rgba(17,100,102,0.7)_100%)]"></div>

        <div class="maison-reveal relative mx-auto flex max-w-[800px] flex-col items-center text-center" {!! $reveal !!}>
            <span class="h-px w-20 bg-accent-gold" aria-hidden="true"></span>
            <p class="mt-8 font-accent italic text-[13px] uppercase tracking-[0.5em] text-accent-gold">An open door</p>
            <h2 id="invitation-title" class="mt-6 font-heading text-[clamp(36px,4.6vw,56px)] leading-[1.1] text-white">
                Begin a conversation with the atelier.
            </h2>
            <p class="mt-6 max-w-[540px] font-accent italic text-lg text-text-mint/85">
                Submit an inquiry, request a piece not yet listed, or request to open a private account.
            </p>

            <div class="mt-10 flex w-full flex-col items-stretch justify-center gap-4 sm:w-auto sm:flex-row">
                <a href="{{ route('contact.index') }}"
                   class="inline-flex items-center justify-center border border-accent-gold bg-accent-gold px-9 py-[18px] text-xs uppercase tracking-[0.3em] text-primary-dark transition duration-300 hover:border-primary-teal hover:bg-primary-teal hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-gold">
                    Request an invitation
                </a>
                <a href="{{ route('shop.index') }}"
                   class="inline-flex items-center justify-center border border-accent-gold px-9 py-[18px] text-xs uppercase tracking-[0.3em] text-accent-gold transition duration-300 hover:bg-accent-gold/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-gold">
                    Browse the catalog
                </a>
            </div>

            <span class="mt-10 h-px w-20 bg-accent-gold/50" aria-hidden="true"></span>
            <p class="mt-6 font-accent italic text-xs text-text-mint/50">Replies within 24 hours · Managed personally by our team</p>
        </div>
    </section>

    {{-- ═══════════════ PRE-FOOTER ═══════════════ --}}
    <section class="bg-surface-ink px-6 py-20 text-center">
        <img src="{{ asset('images/logo/elite-mark.svg') }}" alt="ÉLITE" class="mx-auto h-7 w-7" loading="lazy" decoding="async">
        <p class="mt-5 font-accent italic text-sm uppercase tracking-[0.4em] text-accent-gold">Maison ÉLITE · Prishtina · Horlogère</p>
        <p class="mt-2 text-xs text-text-mint/50">By appointment · Est. 2026</p>
    </section>

</x-app-layout>
