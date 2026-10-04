<x-app-layout>

    <div class="bg-primary-dark min-h-screen pt-16 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-14">
                @if (filled($filters['q'] ?? null))
                    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Search the maison') }}</p>
                    <h1 class="font-heading text-white text-4xl sm:text-5xl">‘{{ $filters['q'] }}’</h1>
                    <a href="{{ route('shop.index', \Illuminate\Support\Arr::except($filters, ['q'])) }}" class="mt-4 inline-block text-xs uppercase tracking-[0.2em] text-text-mint/60 hover:text-accent-gold transition">{{ __('Clear search') }} &times;</a>
                @else
                    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('The Collection') }}</p>
                    <h1 class="font-heading text-white text-4xl sm:text-5xl">{{ __('Shop All Watches') }}</h1>
                @endif
                <div class="hairline-gold w-24 mx-auto mt-6"></div>
            </div>

            <div
                x-data="{
                    filtersOpen: false,
                    view: (localStorage.getItem('elite-shop-view') ?? 'grid'),
                }"
                x-effect="localStorage.setItem('elite-shop-view', view)"
                class="lg:flex lg:items-start lg:gap-10"
            >
                <form method="GET" action="{{ route('shop.index') }}" id="shop-filter-form" class="contents">
                    {{-- Keep a search phrase while filters are refined --}}
                    @if (filled($filters['q'] ?? null))
                        <input type="hidden" name="q" value="{{ $filters['q'] }}">
                    @endif

                    <!-- Mobile filters trigger -->
                    <div class="lg:hidden mb-6">
                        <button
                            type="button"
                            @click="filtersOpen = true"
                            class="inline-flex items-center gap-2 border border-accent-gold text-accent-gold px-5 py-2.5 text-xs uppercase tracking-[0.2em] hover:bg-accent-gold hover:text-primary-dark transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m9 12h3.75M3.75 18H15m0 0a1.5 1.5 0 103 0m-3 0a1.5 1.5 0 113 0M3.75 12H12m9 0h-3.75M12 12a1.5 1.5 0 103 0m-3 0a1.5 1.5 0 113 0" />
                            </svg>
                            {{ __('Filters') }}
                        </button>
                    </div>

                    <!-- Mobile backdrop -->
                    <div
                        x-show="filtersOpen"
                        x-cloak
                        x-transition.opacity
                        @click="filtersOpen = false"
                        class="fixed inset-0 bg-black/70 z-40 lg:hidden"
                    ></div>

                    <!-- Sidebar / drawer -->
                    <aside
                        :class="filtersOpen ? 'translate-x-0' : '-translate-x-full'"
                        class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] overflow-y-auto bg-primary-dark border-r border-primary-teal/30 p-6 transition-transform duration-300 ease-out lg:static lg:z-0 lg:w-72 lg:shrink-0 lg:translate-x-0 lg:border lg:border-primary-teal/20 lg:p-6 lg:sticky lg:top-24"
                    >
                        <div class="flex items-center justify-between mb-8 lg:hidden">
                            <h2 class="font-heading text-white text-xl">{{ __('Filters') }}</h2>
                            <button type="button" @click="filtersOpen = false" class="text-text-mint hover:text-accent-gold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Brand -->
                        <div class="mb-8">
                            <h3 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-4">{{ __('Brand') }}</h3>
                            <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1 scrollbar-hide">
                                @foreach ($brands as $brand)
                                    <label class="flex items-center gap-3 text-sm text-text-mint/80 hover:text-text-mint cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="brand[]"
                                            value="{{ $brand->id }}"
                                            @checked(in_array($brand->id, (array) ($filters['brand'] ?? [])))
                                            @change="$el.form.requestSubmit()"
                                            class="rounded-none border-text-mint/30 bg-transparent text-accent-gold focus:ring-accent-gold focus:ring-offset-0"
                                        >
                                        {{ $brand->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="hairline-gold mb-8"></div>

                        <!-- Category -->
                        <div class="mb-8">
                            <h3 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-4">{{ __('Category') }}</h3>
                            <div class="space-y-2.5">
                                @foreach ($categories as $category)
                                    <label class="flex items-center gap-3 text-sm text-text-mint/80 hover:text-text-mint cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="category[]"
                                            value="{{ $category->id }}"
                                            @checked(in_array($category->id, (array) ($filters['category'] ?? [])))
                                            @change="$el.form.requestSubmit()"
                                            class="rounded-none border-text-mint/30 bg-transparent text-accent-gold focus:ring-accent-gold focus:ring-offset-0"
                                        >
                                        {{ $category->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="hairline-gold mb-8"></div>

                        <!-- Price range -->
                        <div
                            class="mb-8"
                            x-data="{
                                min: {{ (int) ($filters['min_price'] ?? 0) }},
                                max: {{ (int) ($filters['max_price'] ?? 40000) }},
                                floor: 0,
                                ceil: 40000,
                            }"
                        >
                            <h3 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-4">{{ __('Price Range') }}</h3>

                            <div class="flex items-center justify-between text-xs text-accent-gold mb-3">
                                <span x-text="'€' + Number(min).toLocaleString()"></span>
                                <span x-text="'€' + Number(max).toLocaleString()"></span>
                            </div>

                            <div class="range-slider relative h-4 flex items-center">
                                <div class="absolute inset-x-0 h-1 bg-text-mint/20"></div>
                                <div
                                    class="absolute h-1 bg-accent-gold"
                                    :style="`left:${(min / ceil) * 100}%; right:${100 - (max / ceil) * 100}%`"
                                ></div>
                                <input
                                    type="range"
                                    name="min_price"
                                    min="0"
                                    max="40000"
                                    step="500"
                                    x-model.number="min"
                                    @change="if (min > max) min = max; $el.form.requestSubmit()"
                                >
                                <input
                                    type="range"
                                    name="max_price"
                                    min="0"
                                    max="40000"
                                    step="500"
                                    x-model.number="max"
                                    @change="if (max < min) max = min; $el.form.requestSubmit()"
                                >
                            </div>
                        </div>

                        <div class="hairline-gold mb-8"></div>

                        <!-- In stock -->
                        <div class="mb-2">
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="font-heading text-white text-sm uppercase tracking-[0.15em]">{{ __('In stock only') }}</span>
                                <input
                                    type="checkbox"
                                    name="in_stock"
                                    value="1"
                                    @checked(($filters['in_stock'] ?? false))
                                    @change="$el.form.requestSubmit()"
                                    class="rounded-none border-text-mint/30 bg-transparent text-accent-gold focus:ring-accent-gold focus:ring-offset-0"
                                >
                            </label>
                        </div>

                        @if (collect($filters)->filter()->isNotEmpty())
                            <a href="{{ route('shop.index') }}" class="mt-8 inline-block text-xs uppercase tracking-[0.2em] text-text-mint/60 hover:text-accent-gold transition">
                                {{ __('Clear all filters') }}
                            </a>
                        @endif
                    </aside>

                    <!-- Main column -->
                    <div class="flex-1 min-w-0">
                        <!-- Top bar -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10 pb-6 border-b border-primary-teal/20">
                            <p class="text-sm text-text-mint/60">
                                {{ __('Showing') }}
                                <span class="text-text-mint">{{ $watches->firstItem() ?? 0 }}&ndash;{{ $watches->lastItem() ?? 0 }}</span>
                                {{ __('of') }}
                                <span class="text-text-mint">{{ $watches->total() }}</span>
                                {{ __('results') }}
                            </p>

                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-xs uppercase tracking-[0.15em] text-text-mint/70">
                                    {{ __('Sort') }}
                                    <select
                                        name="sort"
                                        @change="$el.form.requestSubmit()"
                                        class="bg-transparent border border-text-mint/20 text-text-mint text-xs py-2 pl-3 pr-8 focus:border-accent-gold focus:ring-0 rounded-none"
                                    >
                                        <option value="newest" class="bg-primary-dark" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('Newest') }}</option>
                                        <option value="popular" class="bg-primary-dark" @selected(($filters['sort'] ?? '') === 'popular')>{{ __('Most Popular') }}</option>
                                        <option value="price_asc" class="bg-primary-dark" @selected(($filters['sort'] ?? '') === 'price_asc')>{{ __('Price: Low to High') }}</option>
                                        <option value="price_desc" class="bg-primary-dark" @selected(($filters['sort'] ?? '') === 'price_desc')>{{ __('Price: High to Low') }}</option>
                                    </select>
                                </label>

                                <div class="hidden sm:flex items-center gap-1 border border-text-mint/20">
                                    <button
                                        type="button"
                                        @click="view = 'grid'"
                                        :class="view === 'grid' ? 'bg-accent-gold text-primary-dark' : 'text-text-mint/60 hover:text-text-mint'"
                                        class="p-2 transition"
                                        aria-label="{{ __('Grid view') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        @click="view = 'list'"
                                        :class="view === 'list' ? 'bg-accent-gold text-primary-dark' : 'text-text-mint/60 hover:text-text-mint'"
                                        class="p-2 transition"
                                        aria-label="{{ __('List view') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if ($watches->isEmpty())
                            <div class="text-center py-24">
                                <p class="font-heading text-2xl text-white mb-3">{{ __('No watches match your filters') }}</p>
                                <p class="text-text-mint/60 mb-6">{{ __('Try adjusting or clearing your filters.') }}</p>
                                <a href="{{ route('shop.index') }}" class="text-accent-gold uppercase text-xs tracking-[0.2em] hover:text-accent-peach transition">{{ __('Clear all filters') }}</a>
                            </div>
                        @else
                            <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14' : 'flex flex-col gap-4'">
                                @foreach ($watches as $watch)
                                    @php
                                        $primaryImage = $watch->images->firstWhere('is_primary', true) ?? $watch->images->first();
                                    @endphp

                                    <div x-show="view === 'grid'">
                                        <x-product-card :watch="$watch" />
                                    </div>

                                    <a
                                        href="{{ route('shop.show', $watch->slug) }}"
                                        x-show="view === 'list'"
                                        x-cloak
                                        class="group flex items-center gap-6 rounded-lg border border-primary-teal/20 hover:border-accent-gold/70 bg-primary-dark/40 p-4 transition"
                                    >
                                        <div class="w-24 h-24 sm:w-32 sm:h-32 shrink-0 media-overlay overflow-hidden rounded-lg bg-primary-teal/10">
                                            @if ($primaryImage)
                                                <img src="{{ $primaryImage->path }}" alt="{{ $watch->name }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] uppercase tracking-[0.2em] text-accent-gold/80">{{ $watch->brand->name }}</p>
                                            <h3 class="font-heading text-lg text-white group-hover:text-accent-peach transition-colors truncate">{{ $watch->name }}</h3>
                                            <p class="text-xs text-text-mint/50 mt-1">{{ $watch->category->name }}</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            @if ($watch->discount_price)
                                                <p class="text-xs text-text-mint/40 line-through">€{{ number_format((float) $watch->price, 0, ',', '.') }}</p>
                                                <p class="font-heading text-accent-gold text-lg">€{{ number_format((float) $watch->discount_price, 0, ',', '.') }}</p>
                                            @else
                                                <p class="font-heading text-accent-gold text-lg">€{{ number_format((float) $watch->price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="mt-16">
                                {{ $watches->links() }}
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
