<x-app-layout>

    <div class="bg-primary-dark min-h-screen pt-16 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Your Selection') }}</p>
                <h1 class="font-heading text-white text-4xl sm:text-5xl">{{ __('Shopping Cart') }}</h1>
                <div class="hairline-gold w-24 mx-auto mt-6"></div>
            </div>

            <livewire:cart />
        </div>
    </div>

</x-app-layout>
