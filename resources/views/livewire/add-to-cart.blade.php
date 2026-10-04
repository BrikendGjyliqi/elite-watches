<div class="flex flex-col sm:flex-row gap-4">
    <div class="flex items-center border border-text-mint/20 w-max">
        <button type="button" wire:click="decrement" class="px-4 py-3 text-text-mint hover:text-accent-gold transition" aria-label="{{ __('Decrease quantity') }}">&minus;</button>
        <span class="w-10 text-center text-white">{{ $quantity }}</span>
        <button type="button" wire:click="increment" class="px-4 py-3 text-text-mint hover:text-accent-gold transition" aria-label="{{ __('Increase quantity') }}">&plus;</button>
    </div>

    <button
        type="button"
        wire:click="add"
        wire:loading.attr="disabled"
        @disabled($watch->stock < 1)
        class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-3.5 text-xs font-sans uppercase tracking-[0.2em] transition duration-300 bg-primary-teal text-white hover:bg-primary-teal/80 hover:shadow-lg hover:shadow-primary-teal/30 disabled:opacity-40 disabled:cursor-not-allowed"
    >
        <span wire:loading.remove wire:target="add">
            {{ $watch->stock < 1 ? __('Out of Stock') : __('Add to Cart') }}
        </span>
        <span wire:loading wire:target="add">{{ __('Adding…') }}</span>
    </button>
</div>
