<x-account-layout active="addresses" :title="__('Addresses')">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
        @forelse ($addresses as $address)
            <div class="border border-primary-teal/20 p-6 relative">
                @if ($address->is_default)
                    <span class="absolute top-4 right-4 text-[10px] uppercase tracking-widest text-accent-gold border border-accent-gold/40 px-2 py-0.5">{{ __('Default') }}</span>
                @endif
                <p class="text-white mb-1">{{ $address->full_name }}</p>
                @if ($address->phone)
                    <p class="text-sm text-text-mint/60 mb-2">{{ $address->phone }}</p>
                @endif
                <p class="text-sm text-text-mint/70">{{ $address->street }}</p>
                <p class="text-sm text-text-mint/70">{{ $address->city }}@if ($address->state), {{ $address->state }}@endif, {{ $address->postal_code }}</p>
                <p class="text-sm text-text-mint/70 mb-5">{{ $address->country }}</p>

                <div class="flex items-center gap-4 text-xs uppercase tracking-widest">
                    @unless ($address->is_default)
                        <form method="POST" action="{{ route('account.addresses.default', $address) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-accent-gold hover:text-accent-peach transition">{{ __('Set as default') }}</button>
                        </form>
                    @endunless
                    <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('{{ __('Delete this address?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-text-mint/50 hover:text-red-400 transition">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-text-mint/60">{{ __('No saved addresses yet.') }}</p>
        @endforelse
    </div>

    <div class="border border-primary-teal/20 p-8 max-w-2xl">
        <h2 class="font-heading text-white text-xl mb-6">{{ __('Add a New Address') }}</h2>

        <form method="POST" action="{{ route('account.addresses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @csrf

            <div class="sm:col-span-2">
                <x-input-label for="full_name" :value="__('Full Name')" />
                <x-text-input id="full_name" name="full_name" :value="old('full_name')" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone (optional)')" />
                <x-text-input id="phone" name="phone" :value="old('phone')" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="country" :value="__('Country')" />
                <x-text-input id="country" name="country" :value="old('country')" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('country')" class="mt-2" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="street" :value="__('Street Address')" />
                <x-text-input id="street" name="street" :value="old('street')" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('street')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" :value="old('city')" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="state" :value="__('State / Province (optional)')" />
                <x-text-input id="state" name="state" :value="old('state')" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('state')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="postal_code" :value="__('Postal Code')" />
                <x-text-input id="postal_code" name="postal_code" :value="old('postal_code')" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
            </div>

            <div class="sm:col-span-2 pt-2">
                <x-btn-primary type="submit" variant="filled">{{ __('Save Address') }}</x-btn-primary>
            </div>
        </form>
    </div>

</x-account-layout>
