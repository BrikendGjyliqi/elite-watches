<x-guest-layout>
    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-3">{{ __('Account Recovery') }}</p>
    <h1 class="font-heading text-white text-3xl mb-6">{{ __('Forgot Password') }}</h1>

    <p class="mb-6 text-sm text-text-mint/70 leading-relaxed">
        {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>
</x-guest-layout>
