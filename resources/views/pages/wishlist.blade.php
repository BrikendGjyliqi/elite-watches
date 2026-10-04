<x-account-layout active="wishlist" :title="__('Wishlist')">

    @if ($wishlists->isEmpty())
        <div class="text-center py-20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-accent-gold/40 mb-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
            <h2 class="font-heading text-3xl text-white mb-4">{{ __('Your wishlist is empty') }}</h2>
            <p class="text-text-mint/70 mb-10 max-w-md mx-auto">
                {{ __('Save the timepieces that catch your eye and come back to them whenever you\'re ready.') }}
            </p>
            <x-btn-primary :href="route('shop.index')" variant="filled">
                {{ __('Explore the Collection') }}
            </x-btn-primary>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach ($wishlists as $entry)
                @php
                    $watch = $entry->watch;
                    $primaryImage = $watch?->images->firstWhere('is_primary', true) ?? $watch?->images->first();
                @endphp

                @if ($watch)
                    <div wire:key="wishlist-{{ $entry->id }}" class="rounded-lg overflow-hidden border border-primary-teal/20 bg-primary-dark/40 group">
                        <a href="{{ route('shop.show', $watch->slug) }}" class="block relative aspect-square overflow-hidden bg-primary-teal/10">
                            @if ($primaryImage)
                                <img src="{{ $primaryImage->path }}" alt="{{ $watch->name }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @endif

                            <form method="POST" action="{{ route('wishlist.toggle', $watch) }}" class="absolute top-3 right-3 z-10" onclick="event.stopPropagation()">
                                @csrf
                                <button type="submit" class="flex items-center justify-center h-9 w-9 bg-primary-dark/80 text-accent-peach hover:text-red-400 transition" aria-label="{{ __('Remove from wishlist') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>
                            </form>
                        </a>

                        <div class="p-5">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-accent-gold/80">{{ $watch->brand->name }}</p>
                            <a href="{{ route('shop.show', $watch->slug) }}" class="font-heading text-lg text-white hover:text-accent-peach transition-colors block truncate">
                                {{ $watch->name }}
                            </a>
                            <p class="font-heading text-accent-gold text-lg mt-2 mb-4">
                                €{{ number_format((float) ($watch->discount_price ?? $watch->price), 0, ',', '.') }}
                            </p>

                            <livewire:add-to-cart :watch="$watch" :key="'wishlist-add-to-cart-'.$watch->id" />
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</x-account-layout>
