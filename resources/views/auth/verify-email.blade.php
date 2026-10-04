<x-guest-layout>
    <p class="font-accent italic text-accent-peach tracking-[0.3em] uppercase text-sm mb-3">{{ __('One Last Step') }}</p>
    <h1 class="font-heading text-white text-3xl mb-6">{{ __('Verify Email') }}</h1>

    <p class="mb-6 text-sm text-text-mint/70 leading-relaxed">
        {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 border border-accent-gold/40 bg-accent-gold/10 text-accent-gold text-sm px-4 py-3">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-text-mint/60 hover:text-accent-gold transition">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
