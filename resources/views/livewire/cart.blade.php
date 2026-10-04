<div>
    @if ($items->isEmpty())
        <div class="text-center py-24 sm:py-32">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-accent-gold/40 mb-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l.75 6.75a3.75 3.75 0 01-7.5 0 3.75 3.75 0 01-7.5 0L4.5 3z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9.75a3.75 3.75 0 007.5 0M13.5 21v-6a1.5 1.5 0 011.5-1.5h1.5a1.5 1.5 0 011.5 1.5v6" />
            </svg>
            <h2 class="font-heading text-3xl sm:text-4xl text-white mb-4">{{ __('Your vault is empty') }}</h2>
            <p class="font-accent italic text-text-mint/70 mb-10 max-w-md mx-auto">
                {{ __('You haven\'t added any timepieces yet. Explore the collection and find a piece worth keeping.') }}
            </p>
            <x-btn-primary :href="route('shop.index')" variant="filled">
                {{ __('Explore the Collection') }}
            </x-btn-primary>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            <!-- Items -->
            <div class="lg:col-span-2 divide-y divide-primary-teal/10 border-y border-primary-teal/10">
                @foreach ($items as $item)
                    @php
                        $watch = $item->watch;
                        $unitPrice = $watch->discount_price ?? $watch->price;
                        $primaryImage = $watch->images->firstWhere('is_primary', true) ?? $watch->images->first();
                    @endphp
                    <div wire:key="cart-item-{{ $item->id }}" class="flex flex-col sm:flex-row gap-4 sm:gap-6 py-6">
                        <a href="{{ route('shop.show', $watch->slug) }}" class="shrink-0 w-full sm:w-28 h-40 sm:h-28 bg-primary-teal/10 rounded-lg overflow-hidden">
                            @if ($primaryImage)
                                <img src="{{ $primaryImage->path }}" alt="{{ $watch->name }}" loading="lazy" class="w-full h-full object-cover">
                            @endif
                        </a>

                        <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] uppercase tracking-[0.2em] text-accent-gold/80">{{ $watch->brand->name }}</p>
                                <a href="{{ route('shop.show', $watch->slug) }}" class="font-heading text-lg text-white hover:text-accent-peach transition-colors block truncate">
                                    {{ $watch->name }}
                                </a>
                                @if ($watch->reference_number)
                                    <p class="font-mono text-xs text-text-mint/50 mt-1">{{ __('Ref.') }} {{ $watch->reference_number }}</p>
                                @endif
                                <p class="text-sm text-accent-gold mt-2 sm:hidden">€{{ number_format((float) $unitPrice, 0, ',', '.') }}</p>
                            </div>

                            <p class="hidden sm:block text-accent-gold w-28 text-right shrink-0">
                                €{{ number_format((float) $unitPrice, 0, ',', '.') }}
                            </p>

                            <div class="flex items-center border border-text-mint/20 w-max shrink-0">
                                <button
                                    type="button"
                                    wire:click="updateQty({{ $item->id }}, {{ $item->quantity - 1 }})"
                                    @disabled($item->quantity <= 1)
                                    class="px-3 py-2 text-text-mint hover:text-accent-gold transition disabled:opacity-30 disabled:cursor-not-allowed"
                                    aria-label="{{ __('Decrease quantity') }}"
                                >&minus;</button>
                                <span class="w-8 text-center text-white text-sm">{{ $item->quantity }}</span>
                                <button
                                    type="button"
                                    wire:click="updateQty({{ $item->id }}, {{ $item->quantity + 1 }})"
                                    class="px-3 py-2 text-text-mint hover:text-accent-gold transition"
                                    aria-label="{{ __('Increase quantity') }}"
                                >&plus;</button>
                            </div>

                            <p class="w-28 text-right font-heading text-accent-gold shrink-0">
                                €{{ number_format((float) $unitPrice * $item->quantity, 0, ',', '.') }}
                            </p>

                            <button
                                type="button"
                                wire:click="remove({{ $item->id }})"
                                wire:confirm="{{ __('Remove this watch from your cart?') }}"
                                class="text-text-mint/50 hover:text-red-400 transition shrink-0"
                                aria-label="{{ __('Remove') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach

                <div class="pt-6">
                    <x-btn-primary :href="route('shop.index')" variant="ghost">
                        &larr; {{ __('Continue Shopping') }}
                    </x-btn-primary>
                </div>
            </div>

            <!-- Order summary -->
            <div class="lg:col-span-1 lg:sticky lg:top-28 border border-primary-teal/20 bg-primary-dark/40 p-8">
                <h2 class="font-heading text-white text-xl uppercase tracking-wide mb-6">{{ __('Order Summary') }}</h2>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-text-mint/70">{{ __('Subtotal') }}</dt>
                        <dd class="text-text-mint">€{{ number_format($totals['subtotal'], 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-text-mint/70">{{ __('Tax (18%)') }}</dt>
                        <dd class="text-text-mint">€{{ number_format($totals['tax'], 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-text-mint/70">{{ __('Shipping') }}</dt>
                        <dd class="text-text-mint">
                            @if ($totals['shipping'] > 0)
                                €{{ number_format($totals['shipping'], 2, ',', '.') }}
                            @else
                                <span class="text-accent-peach">{{ __('Free') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                @if ($totals['shipping'] > 0)
                    <p class="text-xs text-text-mint/50 mt-3">
                        {{ __('Free shipping on orders over :amount.', ['amount' => '€'.number_format(\App\Services\CartService::FREE_SHIPPING_THRESHOLD, 0, ',', '.')]) }}
                    </p>
                @endif

                <div class="hairline-gold my-6"></div>

                <div class="flex justify-between items-baseline mb-8">
                    <span class="font-heading text-white uppercase tracking-wide text-sm">{{ __('Total') }}</span>
                    <span class="font-heading text-accent-gold text-3xl">€{{ number_format($totals['total'], 2, ',', '.') }}</span>
                </div>

                <x-btn-primary :href="route('checkout.index')" variant="filled" class="w-full">
                    {{ __('Continue to Checkout') }}
                </x-btn-primary>
            </div>
        </div>
    @endif
</div>
