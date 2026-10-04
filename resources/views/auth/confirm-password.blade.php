<x-guest-layout>
    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-3">{{ __('Secure Area') }}</p>
    <h1 class="font-heading text-white text-3xl mb-6">{{ __('Confirm Password') }}</h1>

    <p class="mb-6 text-sm text-text-mint/70 leading-relaxed">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
