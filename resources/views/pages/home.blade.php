<x-app-layout :transparent-nav="true">

    {{-- 1 & 2. Navbar (transparent, in layout) + Hero --}}
    <x-hero
        image="{{ asset('images/watches/f77-malachite-mark-i-4038859_720x.webp') }}"
        image-alt="ÉLITE"
    />

    {{-- 3. Marquee brand strip --}}
    <section class="relative bg-primary-dark border-y border-primary-teal/20 py-10 overflow-hidden">
        <div class="scrollbar-hide overflow-hidden">
            <div class="flex w-max animate-marquee">
                @for ($i = 0; $i < 2; $i++)
                    <div class="flex items-center">
                        @foreach ($brands as $brand)
                            <span class="mx-10 shrink-0 font-heading text-2xl sm:text-3xl tracking-[0.1em] text-text-mint grayscale opacity-50 hover:opacity-100 hover:text-accent-gold hover:grayscale-0 transition duration-500 cursor-default">
                                {{ $brand->name }}
                            </span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- 4. Curated for Connoisseurs --}}
    <section id="curated" class="relative bg-primary-dark py-24 sm:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                x-data="{ shown: false }"
                x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                class="text-center max-w-2xl mx-auto mb-16 transition-all duration-700"
            >
                <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Selected by our master horologists') }}</p>
                <h2 class="font-heading text-white text-4xl sm:text-5xl">{{ __('Curated for Connoisseurs') }}</h2>
                <div class="hairline-gold w-24 mx-auto mt-6"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14">
                @foreach ($featuredWatches as $watch)
                    <x-product-card :watch="$watch" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. Split editorial section --}}
    <section class="relative">
        <div
            x-data="{ shown: false }"
            x-intersect.once="shown = true"
            :class="shown ? 'opacity-100' : 'opacity-0'"
            class="relative h-[70vh] min-h-[480px] media-overlay transition-opacity duration-1000"
        >
            <img
                src="https://placehold.co/1920x1080/1c2320/1c2320"
                alt="{{ __('The Craft of Time') }}"
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
            >

            <div class="relative z-10 h-full flex items-end">
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pb-16">
                    <div class="max-w-xl">
                        <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Heritage') }}</p>
                        <h2 class="font-heading text-white text-4xl sm:text-5xl mb-6">{{ __('The Craft of Time') }}</h2>
                        <p class="text-text-mint/90 leading-relaxed mb-8">
                            {{ __('Every timepiece in our maison carries generations of craftsmanship — hand-finished movements, cases shaped by artisans who have devoted lifetimes to their trade. We believe a watch is not merely kept, but inherited; not simply worn, but carried forward.') }}
                        </p>
                        <a href="{{ route('about') }}" class="inline-flex items-center gap-2 text-accent-gold uppercase text-xs tracking-[0.2em] hover:text-accent-peach transition group">
                            {{ __('Discover the story') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. New Arrivals carousel --}}
    @if ($newArrivals->isNotEmpty())
        <section class="relative bg-primary-dark py-24 sm:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-12">
                    <div>
                        <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Just Landed') }}</p>
                        <h2 class="font-heading text-white text-4xl sm:text-5xl">{{ __('New Arrivals') }}</h2>
                    </div>
                    <a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="hidden sm:inline-flex items-center gap-2 text-accent-gold uppercase text-xs tracking-[0.2em] hover:text-accent-peach transition">
                        {{ __('View all') }}
                    </a>
                </div>
            </div>

            <div
                x-data="{}"
                class="flex gap-6 overflow-x-auto scrollbar-hide snap-x snap-mandatory px-4 sm:px-6 lg:px-[max(2rem,calc((100vw-80rem)/2+2rem))] pb-4"
            >
                @foreach ($newArrivals as $watch)
                    <div class="snap-start shrink-0 w-64 sm:w-72">
                        <x-product-card :watch="$watch" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 7. By Category tiles --}}
    @if ($spotlightCategories->isNotEmpty())
        <section class="relative bg-primary-dark pb-24 sm:pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Find your fit') }}</p>
                    <h2 class="font-heading text-white text-4xl sm:text-5xl">{{ __('By Category') }}</h2>
                    <div class="hairline-gold w-24 mx-auto mt-6"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $categoryImages = [
                            'Diver' => 'images/watches/diver.webp',
                            'Dress' => 'images/watches/dress.avif',
                            'Chronograph' => 'images/watches/Chronograph.webp',
                            'Pilot' => 'images/watches/pilot.webp',
                        ];
                    @endphp
                    @foreach ($spotlightCategories as $category)
                        <a
                            href="{{ route('shop.index', ['category' => [$category->id]]) }}"
                            x-data="{ shown: false }"
                            x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                            class="group relative block h-96 media-overlay overflow-hidden rounded-lg transition-all duration-700"
                        >
                            <img
                                src="{{ isset($categoryImages[$category->name]) ? asset($categoryImages[$category->name]) : 'https://placehold.co/600x800/1c2320/1c2320' }}"
                                alt="{{ $category->name }}"
                                loading="lazy"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            >
                            <div class="relative z-10 h-full flex items-end p-6">
                                <h3 class="font-heading text-accent-gold text-2xl uppercase tracking-wide group-hover:text-accent-peach transition-colors">
                                    {{ $category->name }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 8. Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="relative bg-text-mint/[0.04] py-24 sm:py-32 border-y border-primary-teal/20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('In their words') }}</p>
                    <h2 class="font-heading text-white text-4xl sm:text-5xl">{{ __('Trusted by Collectors') }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    @foreach ($testimonials as $review)
                        <div
                            x-data="{ shown: false }"
                            x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                            class="text-center transition-all duration-700"
                        >
                            <div class="flex justify-center gap-1 mb-5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $review->rating ? 'text-accent-gold' : 'text-text-mint/20' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.287 3.955c.299.921-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.363-1.118L2.02 9.382c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.955z" />
                                    </svg>
                                @endfor
                            </div>

                            <p class="font-accent italic text-text-mint text-lg leading-relaxed mb-6">
                                &ldquo;{{ $review->body }}&rdquo;
                            </p>

                            <div class="flex items-center justify-center gap-3">
                                <img
                                    src="https://placehold.co/100x100/116466/D1E8E2?text={{ urlencode(Str::of($review->user->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->join('')) }}"
                                    alt="{{ $review->user->name }}"
                                    class="h-10 w-10 rounded-full object-cover"
                                >
                                <div class="text-left">
                                    <p class="text-sm text-white">{{ $review->user->name }}</p>
                                    <p class="text-xs text-text-mint/60">{{ __('Verified Owner') }} &middot; {{ $review->watch->name }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 9. Newsletter band --}}
    <section class="relative bg-primary-teal/10 py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-heading text-white text-3xl sm:text-4xl mb-4">{{ __('Enter the ÉLITE circle') }}</h2>
            <p class="font-accent italic text-text-mint/80 mb-8">
                {{ __('Private previews, limited releases, and stories from the world of fine watchmaking — delivered rarely, always worth reading.') }}
            </p>

            <form method="POST" action="#" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                @csrf
                <label for="newsletter-email" class="sr-only">{{ __('Email address') }}</label>
                <input
                    id="newsletter-email"
                    type="email"
                    name="email"
                    required
                    placeholder="{{ __('Your email address') }}"
                    class="flex-1 bg-transparent border border-text-mint/30 focus:border-accent-gold px-5 py-3 text-sm text-white placeholder-text-mint/50 focus:outline-none focus:ring-0 rounded-none"
                >
                <x-btn-primary type="submit" variant="filled" class="!bg-accent-gold !text-primary-dark hover:!bg-accent-peach">
                    {{ __('Subscribe') }}
                </x-btn-primary>
            </form>
        </div>
    </section>

</x-app-layout>
