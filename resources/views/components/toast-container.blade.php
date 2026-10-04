<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type: toast.type || 'info', message: toast.message });
            setTimeout(() => this.remove(id), 4500);
        },
        remove(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    }"
    x-on:notify.window="add($event.detail)"
    x-init="
        @if (session('wishlist_status') === 'added') add({ type: 'success', message: '{{ __('Added to your wishlist.') }}' }); @endif
        @if (session('wishlist_status') === 'removed') add({ type: 'info', message: '{{ __('Removed from your wishlist.') }}' }); @endif
        @if (session('status') === 'contact-sent') add({ type: 'success', message: '{{ __('Your message has been sent — we will be in touch shortly.') }}' }); @endif
        @if (session('status') === 'review-submitted') add({ type: 'success', message: '{{ __('Thank you — your review has been submitted for approval.') }}' }); @endif
        @if ($errors->any()) add({ type: 'error', message: @js($errors->first()) }); @endif
    "
    class="fixed top-24 right-4 z-[100] flex flex-col gap-3 w-80 max-w-[90vw] pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto bg-primary-dark border border-accent-gold shadow-xl shadow-black/40 px-5 py-4 flex items-start gap-3"
        >
            <svg x-show="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-gold shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg x-show="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <svg x-show="toast.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-peach shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <p class="text-sm text-text-mint flex-1" x-text="toast.message"></p>
            <button type="button" @click="remove(toast.id)" class="text-text-mint/50 hover:text-accent-gold transition shrink-0" aria-label="{{ __('Dismiss') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
