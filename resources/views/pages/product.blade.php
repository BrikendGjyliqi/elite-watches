@php
    $primaryImage = $watch->images->firstWhere('is_primary', true) ?? $watch->images->first();
    $hasDiscount = ! is_null($watch->discount_price);
    $reviews = $watch->reviews;
    $reviewCount = $reviews->count();
    $averageRating = $reviewCount ? round($reviews->avg('rating'), 1) : 0;
    $ratingCounts = $reviews->countBy('rating');
    $specs = [
        'Movement' => $watch->spec?->movement,
        'Case material' => $watch->spec?->case_material,
        'Case diameter' => $watch->spec?->case_diameter,
        'Case thickness' => $watch->spec?->case_thickness,
        'Dial color' => $watch->spec?->dial_color,
        'Crystal' => $watch->spec?->crystal,
        'Water resistance' => $watch->spec?->water_resistance,
        'Power reserve' => $watch->spec?->power_reserve,
        'Bracelet material' => $watch->spec?->bracelet_material,
        'Weight' => $watch->spec?->weight,
    ];
@endphp

<x-app-layout>

    <div class="bg-primary-dark min-h-screen pt-16 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav class="text-xs text-text-mint/50 mb-10 flex items-center gap-2 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-accent-gold transition">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('shop.index') }}" class="hover:text-accent-gold transition">{{ __('Shop') }}</a>
                <span>/</span>
                <a href="{{ route('brands.show', $watch->brand->slug) }}" class="hover:text-accent-gold transition">{{ $watch->brand->name }}</a>
                <span>/</span>
                <span class="text-text-mint/80">{{ $watch->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-20">

                <!-- Gallery -->
                <div
                    x-data="{
                        images: {{ $watch->images->pluck('path')->toJson() }},
                        active: '{{ $primaryImage?->path }}',
                        zoom: false,
                        bgX: 50,
                        bgY: 50,
                    }"
                >
                    <div
                        class="relative aspect-square overflow-hidden rounded-lg bg-primary-teal/10 border border-primary-teal/20 cursor-zoom-in"
                        @mousemove="const r = $el.getBoundingClientRect(); bgX = ((event.clientX - r.left) / r.width) * 100; bgY = ((event.clientY - r.top) / r.height) * 100;"
                        @mouseenter="zoom = true"
                        @mouseleave="zoom = false"
                        :style="zoom ? `background-image:url('${active}'); background-size:220%; background-position:${bgX}% ${bgY}%;` : ''"
                    >
                        <img :src="active" x-show="!zoom" alt="{{ $watch->name }}" class="w-full h-full object-cover">
                    </div>

                    @if ($watch->images->count() > 1)
                        <div class="flex gap-3 mt-4 overflow-x-auto scrollbar-hide">
                            @foreach ($watch->images as $image)
                                <button
                                    type="button"
                                    @click="active = '{{ $image->path }}'"
                                    :class="active === '{{ $image->path }}' ? 'border-accent-gold' : 'border-transparent hover:border-text-mint/30'"
                                    class="shrink-0 w-20 h-20 rounded-md border transition overflow-hidden bg-primary-teal/10"
                                >
                                    <img src="{{ $image->path }}" alt="{{ $watch->name }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Details -->
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-accent-gold">
                        <a href="{{ route('brands.show', $watch->brand->slug) }}" class="hover:text-accent-peach transition">{{ $watch->brand->name }}</a>
                    </p>

                    <h1 class="font-heading text-white text-4xl sm:text-5xl mt-3 mb-4 leading-tight">
                        {{ $watch->name }}
                    </h1>

                    @if ($watch->reference_number)
                        <p class="font-mono text-sm text-text-mint/60 tracking-wide mb-6">
                            {{ __('Ref.') }} {{ $watch->reference_number }}
                        </p>
                    @endif

                    <div class="flex items-baseline gap-3 mb-8">
                        @if ($hasDiscount)
                            <span class="text-lg text-text-mint/40 line-through">€{{ number_format((float) $watch->price, 0, ',', '.') }}</span>
                            <span class="font-heading text-accent-gold text-3xl sm:text-4xl">€{{ number_format((float) $watch->discount_price, 0, ',', '.') }}</span>
                        @else
                            <span class="font-heading text-accent-gold text-3xl sm:text-4xl">€{{ number_format((float) $watch->price, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    @if ($watch->short_description)
                        <p class="text-text-mint/80 leading-relaxed mb-8 max-w-lg">
                            {{ $watch->short_description }}
                        </p>
                    @endif

                    @if ($reviewCount)
                        <a href="#reviews" class="inline-flex items-center gap-2 mb-8 text-sm text-text-mint/70 hover:text-accent-gold transition">
                            <span class="flex gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= round($averageRating) ? 'text-accent-gold' : 'text-text-mint/20' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.287 3.955c.299.921-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.363-1.118L2.02 9.382c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.955z" />
                                    </svg>
                                @endfor
                            </span>
                            {{ $averageRating }} &middot; {{ trans_choice('{1} :count review|[2,*] :count reviews', $reviewCount, ['count' => $reviewCount]) }}
                        </a>
                    @endif

                    <!-- Quantity + actions -->
                    <div class="space-y-4 mb-10">
                        <livewire:add-to-cart :watch="$watch" :key="'add-to-cart-'.$watch->id" />

                        <form method="POST" action="{{ route('wishlist.toggle', $watch) }}">
                            @csrf
                            <x-btn-primary type="submit" variant="outline" class="w-full">
                                {{ __('Add to Wishlist') }}
                            </x-btn-primary>
                        </form>
                    </div>

                    <!-- Delivery / warranty -->
                    <div class="grid grid-cols-3 gap-4 border-t border-primary-teal/20 pt-8">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto text-accent-gold mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25h5.25a1.125 1.125 0 011.125 1.125v9.75" />
                            </svg>
                            <p class="text-[11px] uppercase tracking-wide text-text-mint/70">{{ __('Free Worldwide Delivery') }}</p>
                        </div>
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto text-accent-gold mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                            </svg>
                            <p class="text-[11px] uppercase tracking-wide text-text-mint/70">{{ __('2-Year International Warranty') }}</p>
                        </div>
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto text-accent-gold mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                            </svg>
                            <p class="text-[11px] uppercase tracking-wide text-text-mint/70">{{ __('Certificate of Authenticity') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div x-data="{ tab: 'description' }" class="mt-20 lg:mt-28">
                <div class="flex gap-8 border-b border-primary-teal/20 overflow-x-auto scrollbar-hide">
                    <button type="button" @click="tab = 'description'" :class="tab === 'description' ? 'text-accent-gold border-accent-gold' : 'text-text-mint/60 border-transparent hover:text-text-mint'" class="pb-4 border-b-2 text-xs uppercase tracking-[0.2em] whitespace-nowrap transition">
                        {{ __('Description') }}
                    </button>
                    <button type="button" @click="tab = 'specifications'" :class="tab === 'specifications' ? 'text-accent-gold border-accent-gold' : 'text-text-mint/60 border-transparent hover:text-text-mint'" class="pb-4 border-b-2 text-xs uppercase tracking-[0.2em] whitespace-nowrap transition">
                        {{ __('Specifications') }}
                    </button>
                    <button type="button" @click="tab = 'reviews'" :class="tab === 'reviews' ? 'text-accent-gold border-accent-gold' : 'text-text-mint/60 border-transparent hover:text-text-mint'" class="pb-4 border-b-2 text-xs uppercase tracking-[0.2em] whitespace-nowrap transition" id="reviews">
                        {{ __('Reviews') }} ({{ $reviewCount }})
                    </button>
                </div>

                <!-- Description -->
                <div x-show="tab === 'description'" class="py-10 max-w-3xl">
                    <p class="text-text-mint/80 leading-relaxed whitespace-pre-line">
                        {{ $watch->description ?: __('No description available yet for this timepiece.') }}
                    </p>
                </div>

                <!-- Specifications -->
                <div x-show="tab === 'specifications'" x-cloak class="py-10 max-w-3xl">
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach ($specs as $label => $value)
                                @if ($value)
                                    <tr class="border-b border-primary-teal/10">
                                        <th scope="row" class="py-3.5 pr-6 text-left font-normal text-text-mint/60 uppercase tracking-wide text-xs w-1/3">{{ $label }}</th>
                                        <td class="py-3.5 text-text-mint">{{ $value }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Reviews -->
                <div x-show="tab === 'reviews'" x-cloak class="py-10">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <!-- Breakdown -->
                        <div>
                            <div class="text-center lg:text-left mb-6">
                                <p class="font-heading text-5xl text-accent-gold">{{ $averageRating }}</p>
                                <div class="flex justify-center lg:justify-start gap-0.5 my-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= round($averageRating) ? 'text-accent-gold' : 'text-text-mint/20' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.287 3.955c.299.921-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.363-1.118L2.02 9.382c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.955z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-xs text-text-mint/50">{{ trans_choice('Based on {1} :count review|[2,*] :count reviews', $reviewCount, ['count' => $reviewCount]) }}</p>
                            </div>

                            <div class="space-y-2">
                                @for ($star = 5; $star >= 1; $star--)
                                    @php $starCount = $ratingCounts[$star] ?? 0; @endphp
                                    <div class="flex items-center gap-3 text-xs text-text-mint/60">
                                        <span class="w-10">{{ $star }} {{ __('star') }}</span>
                                        <div class="flex-1 h-1.5 bg-text-mint/10">
                                            <div class="h-1.5 bg-accent-gold" style="width: {{ $reviewCount ? round($starCount / $reviewCount * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="w-6 text-right">{{ $starCount }}</span>
                                    </div>
                                @endfor
                            </div>

                            @auth
                                <div class="mt-10 border-t border-primary-teal/20 pt-8">
                                    <h3 class="font-heading text-white text-lg mb-4">{{ __('Write a review') }}</h3>

                                    @if (session('status') === 'review-submitted')
                                        <p class="text-sm text-accent-peach mb-4">{{ __('Thank you — your review has been submitted for approval.') }}</p>
                                    @endif

                                    <form method="POST" action="{{ route('reviews.store', $watch->slug) }}" x-data="{ rating: 5 }" class="space-y-4">
                                        @csrf

                                        <div>
                                            <label class="block text-xs uppercase tracking-wide text-text-mint/60 mb-2">{{ __('Rating') }}</label>
                                            <div class="flex gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button" @click="rating = {{ $i }}" class="p-0.5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition" :class="rating >= {{ $i }} ? 'text-accent-gold' : 'text-text-mint/20'" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.287 3.955c.299.921-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.363-1.118L2.02 9.382c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.955z" />
                                                        </svg>
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" x-model="rating">
                                        </div>

                                        <div>
                                            <label for="review-title" class="block text-xs uppercase tracking-wide text-text-mint/60 mb-2">{{ __('Title (optional)') }}</label>
                                            <input id="review-title" type="text" name="title" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 rounded-none">
                                        </div>

                                        <div>
                                            <label for="review-body" class="block text-xs uppercase tracking-wide text-text-mint/60 mb-2">{{ __('Your review') }}</label>
                                            <textarea id="review-body" name="body" rows="4" required class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 rounded-none"></textarea>
                                        </div>

                                        <x-btn-primary type="submit" variant="filled">
                                            {{ __('Submit Review') }}
                                        </x-btn-primary>
                                    </form>
                                </div>
                            @else
                                <p class="mt-10 text-sm text-text-mint/60">
                                    <a href="{{ route('login') }}" class="text-accent-gold hover:text-accent-peach transition">{{ __('Log in') }}</a>
                                    {{ __('to write a review.') }}
                                </p>
                            @endauth
                        </div>

                        <!-- Review list -->
                        <div class="lg:col-span-2 space-y-8">
                            @forelse ($reviews as $review)
                                <div class="border-b border-primary-teal/10 pb-8">
                                    <div class="flex items-center gap-1 mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-accent-gold' : 'text-text-mint/20' }}" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.287 3.955c.299.921-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.363-1.118L2.02 9.382c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.955z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    @if ($review->title)
                                        <h4 class="font-heading text-white text-lg mb-1">{{ $review->title }}</h4>
                                    @endif
                                    <p class="text-text-mint/80 leading-relaxed mb-3">{{ $review->body }}</p>
                                    <p class="text-xs text-text-mint/50">
                                        {{ $review->user->name }} &middot; {{ $review->created_at->format('F Y') }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-text-mint/60">{{ __('No reviews yet — be the first to share your thoughts.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- You may also like -->
            @if ($relatedWatches->isNotEmpty())
                <div class="mt-20 lg:mt-28">
                    <div class="text-center mb-12">
                        <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Curated for you') }}</p>
                        <h2 class="font-heading text-white text-3xl sm:text-4xl">{{ __('You May Also Like') }}</h2>
                    </div>

                    <div class="flex gap-6 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                        @foreach ($relatedWatches as $related)
                            <div class="snap-start shrink-0 w-64 sm:w-72">
                                <x-product-card :watch="$related" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
