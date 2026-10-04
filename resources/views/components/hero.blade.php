@props([
    'eyebrow' => 'MAISON ÉLITE',
    'headline' => 'Time, Refined.',
    'subheadline' => 'A curated house of the world\'s finest timepieces — chosen for those who measure life in moments, not minutes.',
    'discoverHref' => '#curated',
    'shopHref' => null,
    'watches' => null,
    'image' => 'https://placehold.co/900x1200/2C3531/D9B08D?text=Rolex+Daytona',
    'imageAlt' => 'Rolex Daytona',
])

<section class="relative min-h-screen flex flex-col lg:flex-row items-center overflow-hidden bg-primary-dark">
    <div class="grain-overlay"></div>

    <div class="absolute inset-0 bg-gradient-to-br from-primary-dark via-primary-dark to-primary-teal/10"></div>

    <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-28 pb-16 lg:py-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
            <!-- Copy -->
            <div
                x-data="{ shown: false }"
                x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                class="transition-all duration-1000 ease-out text-center lg:text-left"
            >
                <p class="font-accent italic text-accent-peach text-sm sm:text-base tracking-[0.35em] uppercase mb-6">
                    {{ $eyebrow }}
                </p>

                <h1 class="font-heading text-white text-5xl sm:text-6xl lg:text-7xl xl:text-8xl leading-[1.05] mb-6">
                    {{ $headline }}
                </h1>

                <p class="font-accent italic text-text-mint/90 text-lg sm:text-xl max-w-xl mx-auto lg:mx-0 mb-10">
                    {{ $subheadline }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <x-btn-primary :href="$discoverHref" variant="outline">
                        {{ __('Discover') }}
                    </x-btn-primary>
                    <x-btn-primary :href="$shopHref ?? route('shop.index')" variant="filled">
                        {{ __('Shop Now') }}
                    </x-btn-primary>
                </div>
            </div>

            <!-- Hero image -->
            @if ($watches && $watches->isNotEmpty())
            <div class="relative flex justify-center lg:justify-end">
                <div class="absolute w-60 h-60 sm:w-72 sm:h-72 bg-accent-gold/20 rounded-full blur-3xl"></div>

                    <div
                        x-data="{
                            active: 0,
                            count: {{ $watches->count() }},
                            timer: null,
                            next() { this.active = (this.active + 1) % this.count },
                            go(i) { this.active = i; clearInterval(this.timer); this.timer = setInterval(() => this.next(), 3200) },
                        }"
                        x-init="timer = setInterval(() => next(), 3200)"
                        x-on:mouseleave="clearInterval(timer); timer = setInterval(() => next(), 3200)"
                        class="relative w-full max-w-xs sm:max-w-sm lg:max-w-md aspect-square"
                    >
                        @foreach ($watches as $i => $watch)
                            @php $primaryImage = $watch->images->firstWhere('is_primary', true) ?? $watch->images->first(); @endphp
                            <a
                                href="{{ route('shop.show', $watch->slug) }}"
                                x-show="active === {{ $i }}"
                                x-transition:enter="transition ease-out duration-700"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-500"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute inset-0 flex flex-col items-center justify-center media-overlay rounded-xl overflow-hidden shadow-2xl shadow-black/50 group"
                            >
                                @if ($primaryImage)
                                    <img
                                        src="{{ $primaryImage->path }}"
                                        alt="{{ $watch->name }}"
                                        class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105"
                                    >
                                @endif

                                <span class="absolute bottom-4 left-1/2 -translate-x-1/2 whitespace-nowrap px-4 py-1.5 bg-primary-dark/80 border border-accent-gold/40 text-accent-gold text-[11px] uppercase tracking-[0.2em] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    {{ $watch->name }}
                                </span>
                            </a>
                        @endforeach

                        @if ($watches->count() > 1)
                            <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2">
                                @foreach ($watches as $i => $watch)
                                    <button
                                        type="button"
                                        x-on:click="go({{ $i }})"
                                        :class="active === {{ $i }} ? 'w-6 bg-accent-gold' : 'w-2 bg-text-mint/30 hover:bg-text-mint/50'"
                                        class="h-2 rounded-full transition-all duration-300"
                                        aria-label="{{ __('Show :name', ['name' => $watch->name]) }}"
                                    ></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
            </div>
            @endif
        </div>
    </div>

    @unless ($watches && $watches->isNotEmpty())
        <!-- Full-bleed hero image (right half on desktop, edge-to-edge below copy on mobile) -->
        <div
            x-data="{ shown: false }"
            x-init="setTimeout(() => shown = true, 200)"
            :class="shown ? 'opacity-100' : 'opacity-0'"
            class="relative w-full h-[60vh] lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 lg:h-auto overflow-hidden transition-opacity duration-1000 ease-out"
        >
            <img
                src="{{ $image }}"
                alt="{{ $imageAlt }}"
                class="absolute inset-0 w-full h-full object-cover"
            >
            <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(90deg, rgba(44,53,49,0.4) 0%, transparent 30%);"></div>
        </div>
    @endunless

    <!-- Scroll cue -->
    <a href="{{ $discoverHref }}" class="hidden lg:flex absolute bottom-10 left-1/2 -translate-x-1/2 flex-col items-center gap-2 text-text-mint/70 hover:text-accent-gold transition">
        <span class="text-[10px] uppercase tracking-[0.3em]">{{ __('Scroll') }}</span>
        <span class="w-px h-10 bg-gradient-to-b from-accent-gold to-transparent"></span>
    </a>
</section>
