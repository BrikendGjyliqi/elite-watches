<a
    href="{{ route('cart.index') }}"
    x-data="{ pulse: false }"
    x-on:cart-updated.window="pulse = true; setTimeout(() => pulse = false, 600)"
    class="relative text-text-mint hover:text-accent-peach transition"
    aria-label="{{ __('Cart') }}"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.936-4.79 2.398-7.408.106-.6-.372-1.142-.98-1.142H5.106M7.5 14.25L5.106 5.25M7.5 14.25L5.25 12M18 21a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8.25 21a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
    </svg>
    @if ($count > 0)
        <span
            :class="pulse ? 'scale-125' : 'scale-100'"
            class="absolute -top-2 -right-2 inline-flex items-center justify-center h-4 w-4 rounded-full bg-accent-gold text-primary-dark text-[10px] font-semibold transition-transform duration-300 ease-out"
        >
            {{ $count }}
        </span>
    @endif
</a>
