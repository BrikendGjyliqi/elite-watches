<x-guest-layout>
    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-3">{{ __('Welcome Back') }}</p>
    <h1 class="font-heading text-white text-3xl mb-8">{{ __('Sign In') }}</h1>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-text-mint/70">
                <input id="remember_me" type="checkbox" class="rounded-none border-text-mint/30 bg-transparent text-accent-gold focus:ring-accent-gold focus:ring-offset-0" name="remember">
                {{ __('Remember me') }}
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-accent-gold hover:text-accent-peach transition" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Log in') }}
        </x-primary-button>
    </form>

    <p class="mt-10 text-center text-sm text-text-mint/60">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="text-accent-gold hover:text-accent-peach transition">{{ __('Register') }}</a>
    </p>
</x-guest-layout>
