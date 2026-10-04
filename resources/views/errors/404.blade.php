<x-app-layout>

    <div class="bg-primary-dark min-h-screen pt-16 pb-24 flex items-center">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="font-heading text-accent-gold text-8xl sm:text-9xl mb-6">404</p>
            <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-4">{{ __('Lost in Time') }}</p>
            <h1 class="font-heading text-white text-3xl sm:text-4xl mb-6">{{ __('This page could not be found') }}</h1>
            <p class="text-text-mint/70 mb-10 max-w-md mx-auto">
                {{ __('The page you\'re looking for may have been moved, renamed, or never existed. Let\'s get you back on track.') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <x-btn-primary :href="route('home')" variant="filled">{{ __('Back to Home') }}</x-btn-primary>
                <x-btn-primary :href="route('shop.index')" variant="ghost">{{ __('Explore the Collection') }}</x-btn-primary>
            </div>
        </div>
    </div>

</x-app-layout>
