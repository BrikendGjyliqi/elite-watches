{{--
    Photography (Unsplash, placeholder until the maison's own shoot):
      Hero            — photo-1509048191080-d2984bad6ae5 (the brief's fallback)
      Discretion band — photo-1587836374828-4dbafa94cf0e (dial texture at 8%)
      Final band      — photo-1547996160-81dfa63595aa (watch on leather, warm light)
    https://unsplash.com/license
--}}
@php
    $heroImage = 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=2000&q=90';
    $dialImage = 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=1600&q=70';
    $closingImage = 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?w=2000&q=90';

    $contact = config('concierge.contact');
    $telHref = 'tel:'.preg_replace('/[^\d+]/', '', $contact['phone']);
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.$contact['latitude'].','.$contact['longitude'];

    $reveal = 'x-data x-intersect.once.margin.-60px="$el.classList.add(\'is-visible\')"';

    $channels = [
        [
            'numeral' => 'I',
            'eyebrow' => 'Write to us',
            'title' => 'By email',
            'body' => 'For acquisition inquiries, requests for pieces not yet listed, or questions about an existing order.',
            'action' => $contact['email'],
            'href' => 'mailto:'.$contact['email'],
            'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
            'lowercase' => true,
        ],
        [
            'numeral' => 'II',
            'eyebrow' => 'Speak with us',
            'title' => 'By appointment',
            'body' => 'Our concierge receives calls Monday through Saturday, 10:00 to 19:00 Central European Time.',
            'action' => $contact['phone'],
            'href' => $telHref,
            'icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z',
            'lowercase' => false,
        ],
        [
            'numeral' => 'III',
            'eyebrow' => 'Visit the atelier',
            'title' => 'By invitation',
            'body' => 'Private viewings in central Prishtina. Appointments confirmed personally after a brief introduction.',
            'action' => 'Request an appointment',
            'href' => '#written-request',
            'icon' => 'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.699-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z',
            'lowercase' => false,
            'appointment' => true,
        ],
    ];

    $subjects = \App\Models\ContactInquiry::SUBJECTS;
    $channelsForReply = \App\Models\ContactInquiry::CHANNELS;

    // ?subject= pre-selects a subject: a key ("unlisted") or its label ("A piece not listed").
    $requestedSubject = (string) request('subject');
    if (! array_key_exists($requestedSubject, $subjects)) {
        $requestedSubject = array_search($requestedSubject, $subjects, true) ?: null;
    }
    $selectedSubject = old('subject', $requestedSubject);

    // From the search drawer's "Request this piece": start the message with what they searched for.
    // From a maison page's "Request a piece": name the house they were reading about.
    $prefill = trim(\Illuminate\Support\Str::limit(strip_tags((string) request('prefill')), 120, ''));
    $prefillBrand = trim(\Illuminate\Support\Str::limit(strip_tags((string) request('brand')), 60, ''));
    $initialMessage = old('message', match (true) {
        $prefill !== '' => "I am looking for: {$prefill}.\n\n",
        $prefillBrand !== '' => "I am searching for a {$prefillBrand} reference: \n\n",
        default => '',
    });

    $fieldError = fn (string $field) => $errors->has($field) ? ['aria-invalid' => 'true', 'aria-describedby' => "{$field}-error"] : [];
@endphp

<x-app-layout
    :transparent-nav="true"
    seo-title="Contact · ÉLITE Maison Horlogère"
    seo-description="Reach the ÉLITE atelier — by email, by phone, or by appointment. Our concierge replies personally within 24 hours."
    :seo-image="$heroImage"
>
    @push('head')
        <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
        <style>html { scroll-behavior: smooth; }</style>
    @endpush

    {{-- ═══════════════ 1 · HERO ═══════════════ --}}
    <section class="relative h-screen min-h-[620px] overflow-hidden bg-primary-dark" aria-labelledby="concierge-title">
        <img src="{{ $heroImage }}" alt="" class="concierge-zoom absolute inset-0 h-full w-full object-cover" fetchpriority="high" decoding="async">
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(44,53,49,0.3)_0%,rgba(44,53,49,0.95)_100%)]"></div>
        <div class="grain-overlay"></div>

        <div class="relative z-10 mx-auto flex h-full max-w-[1200px] flex-col justify-end px-6 pb-[clamp(96px,12vh,120px)] sm:px-10 lg:px-[120px] xl:px-6">
            <p class="font-accent italic text-sm uppercase tracking-[0.5em] text-accent-gold">Concierge</p>
            <h1 id="concierge-title" class="mt-6 max-w-[820px] font-heading text-[clamp(48px,7vw,88px)] leading-[1.05] text-white">Reach the atelier.</h1>
            <p class="mt-6 max-w-[580px] font-accent italic text-xl text-text-mint/85">
                A member of our team personally reads every message. Replies within {{ config('concierge.sla_hours') }} hours.
            </p>
        </div>

        <a href="#channels" class="absolute bottom-8 left-1/2 z-10 -translate-x-1/2 p-3" aria-label="Scroll to the ways to reach us">
            <span class="maison-scroll-cue block"></span>
        </a>
    </section>

    {{-- ═══════════════ 2 · THREE DOORS ═══════════════ --}}
    <section id="channels" class="scroll-mt-20 bg-primary-dark px-6 py-[clamp(96px,11vw,140px)]" aria-labelledby="channels-title">
        <div class="mx-auto max-w-[1200px]">
            <div class="maison-reveal text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">Channels</p>
                <h2 id="channels-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">Three doors into the maison.</h2>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach ($channels as $card)
                    <article class="concierge-card maison-reveal relative flex flex-col rounded-[2px] border border-accent-gold/[0.12] bg-surface-card px-9 py-12" style="transition-delay: {{ $loop->index * 90 }}ms" {!! $reveal !!}>
                        <span class="absolute right-8 top-8 font-heading text-2xl text-accent-gold/40" aria-hidden="true">{{ $card['numeral'] }}</span>
                        <span class="grid h-11 w-11 place-items-center bg-accent-gold/[0.06] text-accent-gold" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $card['icon'] }}"/></svg>
                        </span>
                        <p class="mt-8 font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">{{ $card['eyebrow'] }}</p>
                        <h3 class="mt-3 font-heading text-[22px] text-white">{{ $card['title'] }}</h3>
                        <p class="mt-4 flex-1 text-sm leading-[1.8] text-text-mint">{{ $card['body'] }}</p>
                        <a
                            href="{{ $card['href'] }}"
                            @if (! empty($card['appointment'])) x-data x-on:click="window.dispatchEvent(new CustomEvent('concierge-subject', { detail: 'appointment' }))" @endif
                            class="group mt-8 inline-flex min-h-[44px] items-center gap-3 self-start text-[13px] tracking-[0.2em] text-accent-gold transition hover:text-accent-peach {{ $card['lowercase'] ? 'normal-case tracking-[0.08em]' : 'uppercase' }}"
                        >
                            {{ $card['action'] }}
                            <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">→</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ 3 · THE WRITTEN REQUEST ═══════════════ --}}
    <section id="written-request" class="scroll-mt-20 bg-surface-ink bg-[radial-gradient(ellipse_at_top,rgba(17,100,102,0.08)_0%,transparent_60%)] px-6 py-[clamp(96px,12vw,160px)]" aria-labelledby="request-title">
        <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-16 md:grid-cols-2 md:gap-12 lg:gap-20">
            {{-- Left: the voice of the atelier --}}
            <div class="maison-reveal" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">A written request</p>
                <h2 id="request-title" class="mt-5 max-w-[480px] font-heading text-[clamp(32px,3.6vw,44px)] leading-[1.15] text-white">Share your inquiry with our atelier.</h2>
                <p class="mt-6 max-w-[440px] text-[15px] leading-[1.9] text-text-mint">
                    Every message is read by a member of our team. We respond personally, usually within {{ config('concierge.sla_hours') }} hours.
                    For urgent acquisitions, please mark your message accordingly.
                </p>

                <figure class="relative mt-12 pl-6">
                    <span class="absolute left-0 top-1 h-[60px] w-[3px] bg-accent-gold" aria-hidden="true"></span>
                    <blockquote class="font-accent italic text-xl text-white">&ldquo;The best conversations begin with a letter.&rdquo;</blockquote>
                    <figcaption class="mt-2 font-accent italic text-xs uppercase tracking-[0.25em] text-accent-gold">— The Atelier</figcaption>
                </figure>

                <ul class="mt-12 space-y-3 text-[13px] text-text-mint">
                    @foreach (['Replies within '.config('concierge.sla_hours').' hours', 'Absolute discretion', 'No sales calls, ever'] as $promise)
                        <li class="flex items-center gap-3">
                            <span class="h-1.5 w-1.5 rotate-45 bg-accent-gold" aria-hidden="true"></span>
                            {{ $promise }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Right: the form --}}
            <div class="maison-reveal rounded-[2px] border border-accent-gold/15 bg-white/[0.02] p-8 backdrop-blur-xl sm:px-12 sm:py-14" {!! $reveal !!}>
                <p class="text-center font-accent italic text-xs uppercase tracking-[0.3em] text-accent-gold">Compose your message</p>
                <div class="maison-divider mt-5" aria-hidden="true"><span></span></div>

                @if ($errors->any())
                    <div class="mt-8 border border-[#E8A598]/35 bg-[#E8A598]/[0.07] px-5 py-4 text-sm text-[#E8A598]" role="alert" tabindex="-1" x-data x-init="$el.focus()">
                        Please review the highlighted {{ str('field')->plural($errors->count()) }} below.
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('contact.store') }}"
                    class="mt-10 space-y-9"
                    novalidate
                    x-data="{
                        submitting: false,
                        message: @js($initialMessage),
                        channel: @js(old('preferred_channel', 'email')),
                        subject: @js($selectedSubject ?? ''),
                    }"
                    x-on:concierge-subject.window="subject = $event.detail"
                    x-on:submit="submitting = true"
                >
                    @csrf

                    {{-- Honeypot: invisible to people, irresistible to bots --}}
                    <div class="absolute -left-[10000px] h-px w-px overflow-hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div>
                        <label for="name" class="concierge-label">Full name <span class="sr-only">(required)</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" required class="concierge-field" @foreach ($fieldError('name') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                        @error('name') <p id="name-error" class="concierge-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="concierge-label">Email address <span class="sr-only">(required)</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" required class="concierge-field" @foreach ($fieldError('email') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                        @error('email') <p id="email-error" class="concierge-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="concierge-label">
                            Phone
                            <span class="normal-case tracking-normal text-text-mint/50" x-text="channel === 'email' ? '(optional)' : '(needed to reach you)'">(optional)</span>
                        </label>
                        <div class="flex items-end gap-3">
                            <label for="phone_prefix" class="sr-only">Country code</label>
                            <input id="phone_prefix" name="phone_prefix" type="text" inputmode="tel" value="{{ old('phone_prefix', '+383') }}" maxlength="5" autocomplete="tel-country-code" class="concierge-field w-[4.5rem] shrink-0 text-accent-gold" @foreach ($fieldError('phone_prefix') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                            <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel-national" placeholder="44 000 000" class="concierge-field" @foreach ($fieldError('phone') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                        </div>
                        @error('phone_prefix') <p id="phone_prefix-error" class="concierge-error">{{ $message }}</p> @enderror
                        @error('phone') <p id="phone-error" class="concierge-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="subject" class="concierge-label">Subject</label>
                        <select id="subject" name="subject" x-model="subject" required class="concierge-field" @foreach ($fieldError('subject') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                            <option value="" disabled>Choose what your message is about</option>
                            @foreach ($subjects as $value => $label)
                                <option value="{{ $value }}" @selected($selectedSubject === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('subject') <p id="subject-error" class="concierge-error">{{ $message }}</p> @enderror
                    </div>

                    <fieldset>
                        <legend class="concierge-label">Preferred reply</legend>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach ($channelsForReply as $value => $label)
                                <label class="relative">
                                    <input type="radio" name="preferred_channel" value="{{ $value }}" x-model="channel" class="peer sr-only" @checked(old('preferred_channel', 'email') === $value)>
                                    <span class="concierge-pill">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('preferred_channel') <p id="preferred_channel-error" class="concierge-error">{{ $message }}</p> @enderror
                    </fieldset>

                    <div>
                        <label for="message" class="concierge-label">Message <span class="sr-only">(required, 20 to 2000 characters)</span></label>
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            minlength="20"
                            maxlength="2000"
                            required
                            x-model="message"
                            placeholder="Share your inquiry in your own words. The more context, the better our reply."
                            class="concierge-field resize-y"
                            @foreach ($fieldError('message') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                        >{{ $initialMessage }}</textarea>
                        <div class="mt-2 flex items-start justify-between gap-4">
                            <div>@error('message') <p id="message-error" class="concierge-error !mt-0">{{ $message }}</p> @enderror</div>
                            <p class="shrink-0 font-accent italic text-sm text-text-mint/50" aria-live="polite"><span x-text="message.length">{{ mb_strlen($initialMessage) }}</span> / 2000</p>
                        </div>
                    </div>

                    <div>
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="consent" value="1" @checked(old('consent')) class="mt-0.5 h-4 w-4 shrink-0 rounded-none border-text-mint/40 bg-transparent text-accent-gold focus:ring-accent-gold focus:ring-offset-0" @foreach ($fieldError('consent') as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                            <span class="text-[13px] leading-relaxed text-text-mint/80">I understand that ÉLITE will contact me regarding this inquiry. We will not share your details.</span>
                        </label>
                        @error('consent') <p id="consent-error" class="concierge-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="!mt-10">
                        <button type="submit" class="concierge-submit flex min-h-[52px] w-full items-center justify-center gap-3 px-6 py-[18px] text-[13px] uppercase" x-bind:disabled="submitting">
                            <span x-show="submitting" x-cloak class="concierge-spinner" aria-hidden="true"></span>
                            <span x-text="submitting ? 'Sending…' : 'Send to the atelier'">Send to the atelier</span>
                        </button>
                        <p class="mt-4 text-center font-accent italic text-[11px] text-text-mint/50">Protected by TLS · Your message is encrypted in transit.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ═══════════════ 4 · WHERE TO FIND US ═══════════════ --}}
    <section class="bg-primary-dark px-6 py-[clamp(96px,11vw,140px)]" aria-labelledby="where-title">
        <div class="mx-auto max-w-[1200px]">
            <div class="maison-reveal text-center" {!! $reveal !!}>
                <p class="font-accent italic text-xs uppercase tracking-[0.4em] text-accent-gold">The Atelier</p>
                <h2 id="where-title" class="mt-5 font-heading text-[clamp(32px,3.4vw,42px)] text-white">Prishtina, Kosovo.</h2>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- Illustrative map: no third-party map requests, in keeping with the discretion promise --}}
                <figure class="maison-reveal relative h-[480px] overflow-hidden border border-accent-gold/40 bg-surface-ink" {!! $reveal !!}>
                    <svg viewBox="0 0 600 480" preserveAspectRatio="xMidYMid slice" class="h-full w-full" role="img" aria-label="Illustrative map of central Prishtina with the atelier marked on Rr. Nëna Terezë">
                        <defs>
                            <pattern id="concierge-blocks" width="40" height="40" patternUnits="userSpaceOnUse" patternTransform="rotate(-8)">
                                <path d="M0 0H40V40" fill="none" stroke="rgba(209,232,226,0.05)" stroke-width="1" />
                            </pattern>
                            <radialGradient id="concierge-glow" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="rgba(217,176,141,0.18)" />
                                <stop offset="100%" stop-color="rgba(217,176,141,0)" />
                            </radialGradient>
                        </defs>
                        <rect width="600" height="480" fill="url(#concierge-blocks)" />
                        {{-- secondary streets --}}
                        <g fill="none" stroke="rgba(209,232,226,0.14)" stroke-width="2" stroke-linecap="round">
                            <path d="M40 120 C 180 108, 420 140, 580 128" />
                            <path d="M20 330 C 200 318, 380 350, 590 340" />
                            <path d="M120 20 C 132 160, 150 320, 140 470" />
                            <path d="M470 10 C 452 160, 480 320, 460 470" />
                            <path d="M210 30 L 380 200 L 560 260" />
                            <path d="M60 420 L 250 290 L 330 250" />
                        </g>
                        {{-- park --}}
                        <path d="M410 360 q 60 -30 120 10 q 20 50 -40 80 q -70 10 -90 -40 z" fill="rgba(17,100,102,0.18)" stroke="rgba(17,100,102,0.35)" />
                        {{-- the pedestrian boulevard --}}
                        <path d="M262 -10 C 280 120, 318 300, 336 490" fill="none" stroke="rgba(217,176,141,0.45)" stroke-width="7" stroke-linecap="round" />
                        <path d="M262 -10 C 280 120, 318 300, 336 490" fill="none" stroke="rgba(26,33,36,1)" stroke-width="3" stroke-dasharray="2 10" />
                        <text x="342" y="150" fill="rgba(217,176,141,0.75)" style="font-family:'Cormorant Garamond',serif; font-style:italic; font-size:15px; letter-spacing:2px;" transform="rotate(80 342 150)">Bulevardi Nëna Terezë</text>
                        {{-- pin --}}
                        <circle cx="300" cy="240" r="90" fill="url(#concierge-glow)" />
                        <circle cx="300" cy="240" r="10" fill="none" stroke="#D9B08D" stroke-width="1.5" class="concierge-pin-pulse" />
                        <path d="M300 252 C 300 252, 284 232, 284 222 a16 16 0 1 1 32 0 C 316 232, 300 252, 300 252 Z" fill="#D9B08D" />
                        <circle cx="300" cy="221" r="5" fill="#1A2124" />
                        <text x="300" y="282" text-anchor="middle" fill="#ffffff" style="font-family:'Playfair Display',serif; font-size:15px; letter-spacing:4px;">ÉLITE</text>
                        {{-- compass --}}
                        <g transform="translate(548 56)" fill="none" stroke="rgba(217,176,141,0.6)" stroke-width="1">
                            <circle r="16" />
                            <path d="M0 -12 L4 2 L0 -1 L-4 2 Z" fill="rgba(217,176,141,0.6)" />
                            <text y="-22" text-anchor="middle" fill="rgba(217,176,141,0.7)" stroke="none" style="font-family:'Inter',sans-serif; font-size:10px; letter-spacing:2px;">N</text>
                        </g>
                    </svg>
                    <figcaption class="absolute bottom-4 left-5 font-accent italic text-xs text-text-mint/55">Illustrative map · exact entrance shared when your visit is confirmed</figcaption>
                </figure>

                {{-- The atelier card --}}
                <div class="maison-reveal flex flex-col rounded-[2px] border border-accent-gold/[0.12] bg-surface-card p-8 sm:p-12" {!! $reveal !!}>
                    <p class="font-accent italic text-xs uppercase tracking-[0.3em] text-accent-gold">Atelier · By appointment</p>
                    <address class="mt-4 not-italic">
                        <span class="block font-heading text-xl text-white">{{ $contact['street'] }}</span>
                        <span class="mt-1 block text-[15px] text-text-mint">{{ $contact['district'] }}</span>
                        <span class="block text-[15px] text-text-mint">{{ $contact['city'] }}</span>
                    </address>

                    <span class="my-8 block h-px w-full bg-accent-gold/20" aria-hidden="true"></span>

                    <p class="font-accent italic text-xs uppercase tracking-[0.3em] text-accent-gold">Concierge hours</p>
                    <dl class="mt-5 divide-y divide-accent-gold/10 text-[13px]">
                        @foreach ($contact['hours'] as $row)
                            <div class="flex items-baseline justify-between gap-4 py-2.5">
                                <dt class="text-text-mint">{{ $row['days'] }}</dt>
                                @if ($row['time'])
                                    <dd class="tabular-nums text-text-mint">{{ $row['time'] }}</dd>
                                @else
                                    <dd class="font-accent italic text-[15px] text-accent-gold">By appointment only</dd>
                                @endif
                            </div>
                        @endforeach
                    </dl>
                    <p class="mt-3 font-accent italic text-xs text-text-mint/60">{{ $contact['timezone_note'] }}</p>

                    <span class="my-8 block h-px w-full bg-accent-gold/20" aria-hidden="true"></span>

                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="mt-auto inline-flex min-h-[48px] items-center justify-center self-start border border-accent-gold px-7 py-3 text-xs uppercase tracking-[0.25em] text-accent-gold transition hover:bg-accent-gold hover:text-primary-dark">
                        Open in Maps <span class="ml-2" aria-hidden="true">→</span><span class="sr-only">(opens Google Maps in a new tab)</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════ 5 · ON DISCRETION ═══════════════ --}}
    <section class="relative overflow-hidden bg-surface-ink px-6 py-[100px]" aria-labelledby="discretion-title">
        <img src="{{ $dialImage }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-[0.08]" loading="lazy" decoding="async">
        <div class="maison-reveal relative mx-auto max-w-[760px] text-center" {!! $reveal !!}>
            <div class="maison-divider" aria-hidden="true"><span></span></div>
            <p class="mt-10 font-accent italic text-xs uppercase tracking-[0.5em] text-accent-gold">On discretion</p>
            <h2 id="discretion-title" class="mt-5 font-heading text-[clamp(26px,3vw,32px)] leading-snug text-white">What stays in the atelier, stays in the atelier.</h2>
            <p class="mt-6 text-[15px] leading-[1.9] text-text-mint">
                Every inquiry, every acquisition, every conversation is treated as privileged. We do not share client information
                with third parties, we do not maintain mailing lists, and we do not advertise our clientele. Your relationship
                with ÉLITE is yours alone.
            </p>

            <ul class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-3">
                @foreach ([
                    ['label' => 'TLS encrypted', 'icon' => 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'],
                    ['label' => 'GDPR compliant', 'icon' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
                    ['label' => 'Never shared', 'icon' => 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88'],
                ] as $badge)
                    <li class="flex flex-col items-center gap-3">
                        <svg class="h-6 w-6 text-accent-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $badge['icon'] }}"/></svg>
                        <span class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">{{ $badge['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══════════════ 6 · WHEN YOU ARE READY ═══════════════ --}}
    <section class="relative flex min-h-[50vh] items-center overflow-hidden px-6 py-28" aria-labelledby="ready-title">
        <img src="{{ $closingImage }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(44,53,49,0.9)_0%,rgba(17,100,102,0.7)_100%)]"></div>

        <div class="maison-reveal relative mx-auto flex max-w-[760px] flex-col items-center text-center" {!! $reveal !!}>
            <span class="h-px w-20 bg-accent-gold" aria-hidden="true"></span>
            <p class="mt-8 font-accent italic text-[13px] uppercase tracking-[0.5em] text-accent-gold">When you are ready</p>
            <h2 id="ready-title" class="mt-6 font-heading text-[clamp(34px,4vw,48px)] leading-[1.12] text-white">Our door is open to those who care for time.</h2>
            <p class="mt-6 max-w-[520px] font-accent italic text-[17px] text-text-mint/85">
                Whether you are beginning your first collection or searching for a particular piece, we welcome your message.
            </p>
            <a href="{{ route('shop.index') }}" class="mt-10 inline-flex min-h-[52px] items-center justify-center border border-accent-gold bg-accent-gold px-9 py-[18px] text-xs uppercase tracking-[0.3em] text-primary-dark transition duration-300 hover:border-primary-teal hover:bg-transparent hover:text-white hover:shadow-[inset_0_0_0_1px_theme(colors.primary.teal)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-gold">
                Browse the catalog
            </a>
            <p class="mt-8 font-accent italic text-xs text-text-mint/50">ÉLITE · Maison Horlogère · Prishtina</p>
        </div>
    </section>

    {{-- ═══════════════ THANK-YOU OVERLAY ═══════════════ --}}
    @if (session('inquiry_sent'))
        <div
            x-data="{
                open: false,
                close() {
                    this.open = false;
                    document.documentElement.style.overflow = '';
                    this.$nextTick(() => document.getElementById('written-request')?.focus?.());
                },
            }"
            x-init="$nextTick(() => { open = true; document.documentElement.style.overflow = 'hidden'; $nextTick(() => $refs.card.focus()) })"
            x-on:keydown.escape.window="if (open) close()"
            x-show="open"
            x-transition:enter="transition duration-500 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#1a2124]/80 px-4 backdrop-blur-sm"
            x-on:click.self="close()"
            role="dialog"
            aria-modal="true"
            aria-labelledby="inquiry-sent-title"
            aria-describedby="inquiry-sent-body"
        >
            <div
                x-ref="card"
                tabindex="-1"
                x-show="open"
                x-transition:enter="transition duration-500 ease-out delay-100"
                x-transition:enter-start="opacity-0 translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="relative w-full max-w-[520px] border border-accent-gold bg-primary-dark px-8 py-12 text-center shadow-2xl outline-none sm:p-14"
            >
                <button type="button" x-on:click="close()" class="absolute right-4 top-4 grid h-11 w-11 place-items-center text-text-mint/60 transition hover:text-accent-gold" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                </button>

                <div class="maison-divider" aria-hidden="true"><span></span></div>

                <svg class="mx-auto mt-8 h-20 w-20 text-accent-gold" viewBox="0 0 84 84" fill="none" aria-hidden="true">
                    <circle class="concierge-check-ring" cx="42" cy="42" r="40" stroke="currentColor" stroke-width="1" />
                    <path class="concierge-check" d="M27 43.5 37.5 54 58 31" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <h2 id="inquiry-sent-title" class="mt-8 font-heading text-[clamp(26px,3vw,32px)] text-white">Your message is in flight.</h2>
                <p id="inquiry-sent-body" class="mt-4 font-accent italic text-[15px] leading-relaxed text-text-mint/80">
                    A member of our atelier will reply within {{ config('concierge.sla_hours') }} hours — usually sooner. Keep an eye on your inbox.
                </p>
                @if (session('inquiry_reference'))
                    <p class="mt-5 text-xs tracking-[0.1em] text-text-mint/50">Reference · {{ session('inquiry_reference') }}</p>
                @endif

                <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('home') }}" class="inline-flex min-h-[44px] items-center justify-center border border-accent-gold/60 px-5 py-3 text-[11px] uppercase tracking-[0.25em] text-accent-gold transition hover:border-accent-gold hover:bg-accent-gold/10">Return to the boutique</a>
                    <a href="{{ route('shop.index') }}" class="inline-flex min-h-[44px] items-center justify-center border border-accent-gold/60 px-5 py-3 text-[11px] uppercase tracking-[0.25em] text-accent-gold transition hover:border-accent-gold hover:bg-accent-gold/10">Browse the catalog</a>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
