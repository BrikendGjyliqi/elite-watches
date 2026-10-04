<div
    x-data="{ show: false }"
    x-init="show = ! localStorage.getItem('elite-cookie-consent')"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="fixed bottom-0 inset-x-0 z-[90] bg-primary-dark/95 backdrop-blur-md border-t border-accent-gold/40"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs text-text-mint/80 text-center sm:text-left">
            {{ __('We use cookies to enhance your experience and remember your preferences. By continuing to browse ÉLITE, you accept our use of cookies.') }}
        </p>
        <button
            type="button"
            @click="show = false; localStorage.setItem('elite-cookie-consent', '1')"
            class="shrink-0 bg-accent-gold text-primary-dark text-xs uppercase tracking-[0.2em] px-6 py-2.5 hover:bg-accent-peach transition"
        >
            {{ __('Accept') }}
        </button>
    </div>
</div>
