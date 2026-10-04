@php
    $addressFields = ['address_id', 'full_name', 'phone', 'street', 'city', 'state', 'postal_code', 'country'];
    $startStep = $errors->hasAny($addressFields) ? 1 : ($errors->hasAny(['preferred_method', 'customer_note', 'acknowledged']) ? 2 : 1);

    // Outline glyphs for the settlement cards (24×24, stroke).
    $methodIcons = [
        'wire' => 'M12 3 3 7.5h18L12 3Zm-7 6.5v7m4.667-7v7m4.666-7v7M19 9.5v7M3 19.5h18M2.5 21.5h19',
        'boutique' => 'M3.5 9.5V20h17V9.5M2.5 9.5 4.5 4h15l2 5.5M2.5 9.5h19M2.5 9.5a2.4 2.4 0 0 0 4.75 0 2.4 2.4 0 0 0 4.75 0 2.4 2.4 0 0 0 4.75 0 2.4 2.4 0 0 0 4.75 0M9.5 20v-5.5h5V20',
        'financing' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
        'crypto' => 'M6 3.5h12l3.5 5.25L12 20.5 2.5 8.75 6 3.5Zm-3.5 5.25h19M9 3.5l-1.5 5.25L12 20.5l4.5-11.75L15 3.5',
    ];
@endphp

<x-app-layout>

    <div class="bg-primary-dark min-h-screen pt-16 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h1 class="font-heading text-white text-4xl sm:text-5xl">{{ __('Private Acquisition') }}</h1>
                <p class="font-accent italic text-accent-gold tracking-[0.3em] text-sm mt-4">{{ __('By appointment · Reviewed personally by our atelier') }}</p>
                <div class="hairline-gold w-24 mx-auto mt-6"></div>
            </div>

            <form
                method="POST"
                action="{{ route('checkout.submit') }}"
                x-data="acquisitionWizard({
                    step: {{ $startStep }},
                    addresses: {{ Js::from($addresses->map(fn ($a) => [
                        'id' => $a->id,
                        'full_name' => $a->full_name,
                        'phone' => $a->phone,
                        'street' => $a->street,
                        'city' => $a->city,
                        'state' => $a->state,
                        'postal_code' => $a->postal_code,
                        'country' => $a->country,
                        'is_default' => $a->is_default,
                    ])) }},
                    old: {{ Js::from([
                        'address_id' => old('address_id'),
                        'full_name' => old('full_name', ''),
                        'phone' => old('phone', ''),
                        'street' => old('street', ''),
                        'city' => old('city', ''),
                        'state' => old('state', ''),
                        'postal_code' => old('postal_code', ''),
                        'country' => old('country', ''),
                        'preferred_method' => old('preferred_method'),
                        'customer_note' => old('customer_note', ''),
                        'acknowledged' => (bool) old('acknowledged'),
                        'submitted' => session()->hasOldInput(),
                    ]) }},
                    methods: {{ Js::from(collect($methods)->map(fn ($m) => $m['label'])) }},
                })"
                x-on:submit="submitting = true"
                class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start"
                novalidate
            >
                @csrf

                <!-- Steps -->
                <div class="lg:col-span-2">
                    <!-- Step indicator -->
                    <div class="flex items-center gap-3 mb-12">
                        <template x-for="(label, index) in ['Contact & Shipping', 'Preferences', 'Review']" :key="index">
                            <div class="flex items-center gap-3" :class="index > 0 && 'flex-1'">
                                <template x-if="index > 0">
                                    <div class="h-px flex-1" :class="step > index ? 'bg-accent-gold' : 'bg-primary-teal/20'"></div>
                                </template>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span
                                        class="flex items-center justify-center h-8 w-8 text-xs border rounded-full transition"
                                        :class="step === index + 1 ? 'border-accent-gold text-accent-gold' : (step > index + 1 ? 'bg-accent-gold border-accent-gold text-primary-dark' : 'border-text-mint/30 text-text-mint/50')"
                                        x-text="index + 1"
                                    ></span>
                                    <span class="hidden sm:inline text-xs uppercase tracking-[0.15em]" :class="step === index + 1 ? 'text-white' : 'text-text-mint/50'" x-text="label"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    @if ($errors->any())
                        <div class="mb-8 border border-[#E8A598]/40 bg-[#E8A598]/10 text-[#E8A598] text-sm px-5 py-4" role="alert">
                            <ul class="space-y-1">
                                @foreach (array_unique($errors->all()) as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p x-show="error" x-cloak x-text="error" role="alert" class="mb-8 border border-[#E8A598]/40 bg-[#E8A598]/10 text-[#E8A598] text-sm px-5 py-4"></p>

                    <!-- Step 1: Contact & Shipping -->
                    <div x-show="step === 1" x-cloak>
                        <h2 class="font-heading text-white text-xl uppercase tracking-wide mb-6">{{ __('Contact & Shipping Address') }}</h2>

                        <template x-if="addresses.length > 0">
                            <div class="space-y-3 mb-8">
                                <template x-for="addr in addresses" :key="addr.id">
                                    <label
                                        class="flex items-start gap-4 border p-5 cursor-pointer transition"
                                        :class="selectedAddressId === addr.id ? 'border-accent-gold bg-accent-gold/5' : 'border-primary-teal/20 hover:border-primary-teal/40'"
                                    >
                                        <input type="radio" name="address_id" class="mt-1 text-accent-gold focus:ring-accent-gold" :value="addr.id" x-model.number="selectedAddressId">
                                        <span class="text-sm">
                                            <span class="text-white block" x-text="addr.full_name"></span>
                                            <span class="text-text-mint/60 block mt-1" x-text="[addr.street, addr.city, addr.postal_code, addr.country].filter(Boolean).join(', ')"></span>
                                        </span>
                                    </label>
                                </template>
                                <label
                                    class="flex items-center gap-4 border p-5 cursor-pointer transition"
                                    :class="selectedAddressId === null ? 'border-accent-gold bg-accent-gold/5' : 'border-primary-teal/20 hover:border-primary-teal/40'"
                                >
                                    <input type="radio" name="address_id" class="text-accent-gold focus:ring-accent-gold" value="" x-on:change="selectedAddressId = null" :checked="selectedAddressId === null">
                                    <span class="text-sm text-white">{{ __('Use a new address') }}</span>
                                </label>
                            </div>
                        </template>

                        <div x-show="selectedAddressId === null" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label for="full_name" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('Full Name') }}</label>
                                <input id="full_name" name="full_name" type="text" x-model="form.full_name" autocomplete="name" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="phone" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('Phone') }}</label>
                                <input id="phone" name="phone" type="text" x-model="form.phone" autocomplete="tel" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="street" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('Street Address') }}</label>
                                <input id="street" name="street" type="text" x-model="form.street" autocomplete="street-address" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                            <div>
                                <label for="city" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('City') }}</label>
                                <input id="city" name="city" type="text" x-model="form.city" autocomplete="address-level2" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                            <div>
                                <label for="state" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('State / Region') }}</label>
                                <input id="state" name="state" type="text" x-model="form.state" autocomplete="address-level1" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                            <div>
                                <label for="postal_code" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('Postal Code') }}</label>
                                <input id="postal_code" name="postal_code" type="text" x-model="form.postal_code" autocomplete="postal-code" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                            <div>
                                <label for="country" class="block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2">{{ __('Country') }}</label>
                                <input id="country" name="country" type="text" x-model="form.country" autocomplete="country-name" class="w-full bg-transparent border border-text-mint/20 focus:border-accent-gold px-4 py-3 text-sm text-white focus:outline-none focus:ring-0">
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <x-btn-primary type="button" variant="filled" @click="toPreferences()">
                                {{ __('Continue to Preferences') }}
                            </x-btn-primary>
                        </div>
                    </div>

                    <!-- Step 2: Preferences -->
                    <div x-show="step === 2" x-cloak>
                        <p class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em] mb-6">{{ __('Preferred method of settlement') }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" role="radiogroup" aria-label="{{ __('Preferred method of settlement') }}">
                            @foreach ($methods as $key => $method)
                                <label
                                    class="acq-method relative flex flex-col gap-3 p-6 cursor-pointer bg-primary-dark/60 transition duration-150"
                                    :class="preferredMethod === @js($key) ? 'is-selected' : ''"
                                >
                                    <input type="radio" name="preferred_method" value="{{ $key }}" x-model="preferredMethod" class="sr-only peer">
                                    <span class="flex items-start justify-between">
                                        <svg class="h-6 w-6 text-accent-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="{{ $methodIcons[$key] }}" />
                                        </svg>
                                        <span class="acq-radio" aria-hidden="true"></span>
                                    </span>
                                    <span class="font-heading text-white text-lg leading-snug">{{ $method['label'] }}</span>
                                    <span class="text-[13px] leading-relaxed text-text-mint/80">{{ $method['description'] }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            <label for="customer_note" class="block font-accent italic text-accent-gold text-[11px] uppercase tracking-[0.3em] mb-2">{{ __('A note to our atelier (optional)') }}</label>
                            <textarea
                                id="customer_note"
                                name="customer_note"
                                rows="4"
                                maxlength="1000"
                                x-model="note"
                                placeholder="{{ __('Share any preferences — sizing, engraving, delivery timing, or questions for our team...') }}"
                                class="w-full bg-transparent border-0 border-b border-text-mint/20 focus:border-accent-gold px-0.5 py-3 text-sm text-white placeholder:text-text-mint/30 focus:outline-none focus:ring-0 resize-y transition-colors"
                            ></textarea>
                            <p class="text-right font-accent italic text-sm text-text-mint/50 mt-1" aria-live="polite"><span x-text="note.length">0</span> / 1000</p>
                        </div>

                        <label class="mt-8 flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="acknowledged" value="1" x-model="acknowledged" class="mt-0.5 h-4 w-4 shrink-0 rounded-none border-text-mint/40 bg-transparent text-accent-gold focus:ring-accent-gold focus:ring-offset-0">
                            <span class="text-sm leading-relaxed text-text-mint/80">
                                {{ __('I understand this is an acquisition request. ÉLITE will contact me within :hours hours to confirm availability, final pricing, and arrange settlement.', ['hours' => config('concierge.sla_hours')]) }}
                            </span>
                        </label>

                        <div class="mt-10 flex justify-between">
                            <x-btn-primary type="button" variant="ghost" @click="step = 1">&larr; {{ __('Back') }}</x-btn-primary>
                            <x-btn-primary type="button" variant="filled" @click="toReview()">{{ __('Review Request') }}</x-btn-primary>
                        </div>
                    </div>

                    <!-- Step 3: Review -->
                    <div x-show="step === 3" x-cloak>
                        <h2 class="font-heading text-white text-xl uppercase tracking-wide mb-6">{{ __('Review Your Request') }}</h2>

                        <div class="divide-y divide-primary-teal/10 border-y border-primary-teal/10 mb-8">
                            @foreach ($items as $item)
                                @php $unitPrice = $item->watch->discount_price ?? $item->watch->price; @endphp
                                <div class="flex items-center justify-between gap-4 py-4 text-sm">
                                    <span>
                                        <span class="block font-accent italic text-accent-gold text-xs uppercase tracking-[0.25em]">{{ $item->watch->brand?->name }}</span>
                                        <span class="text-white">{{ $item->watch->name }}</span>
                                        <span class="text-text-mint/50"> &times; {{ $item->quantity }}</span>
                                    </span>
                                    <span class="font-heading text-accent-gold whitespace-nowrap">€{{ number_format((float) $unitPrice * $item->quantity, 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <div class="border border-primary-teal/20 p-5">
                                <dt class="font-accent italic text-accent-gold text-[11px] uppercase tracking-[0.3em] mb-2">{{ __('Shipping to') }}</dt>
                                <dd class="text-sm text-text-mint" x-text="addressSummary()"></dd>
                            </div>
                            <div class="border border-primary-teal/20 p-5">
                                <dt class="font-accent italic text-accent-gold text-[11px] uppercase tracking-[0.3em] mb-2">{{ __('Preferred settlement') }}</dt>
                                <dd class="font-heading text-white" x-text="methods[preferredMethod] ?? '—'"></dd>
                            </div>
                            <div class="sm:col-span-2 border border-primary-teal/20 p-5" x-show="note.trim().length" x-cloak>
                                <dt class="font-accent italic text-accent-gold text-[11px] uppercase tracking-[0.3em] mb-2">{{ __('Your note') }}</dt>
                                <dd class="font-accent italic text-lg text-text-mint whitespace-pre-line" x-text="note"></dd>
                            </div>
                        </dl>

                        <p class="text-sm text-text-mint/60 leading-relaxed">
                            {{ __('No payment is taken now. Our atelier will contact you within :hours hours.', ['hours' => config('concierge.sla_hours')]) }}
                        </p>

                        <div class="mt-10 flex flex-col-reverse sm:flex-row justify-between items-center gap-4">
                            <x-btn-primary type="button" variant="ghost" @click="step = 2">&larr; {{ __('Back') }}</x-btn-primary>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-8 py-4 border border-accent-gold text-accent-gold text-xs uppercase tracking-[0.3em] transition duration-300 hover:bg-accent-gold hover:text-primary-dark disabled:opacity-60 disabled:cursor-wait"
                                x-bind:disabled="submitting"
                            >
                                <span x-show="!submitting">{{ __('Submit Acquisition Request') }}</span>
                                <span x-show="submitting" x-cloak>{{ __('Submitting…') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="lg:col-span-1 lg:sticky lg:top-28 border border-primary-teal/20 bg-primary-dark/40 p-8">
                    <h2 class="font-heading text-white text-xl uppercase tracking-wide mb-6">{{ __('Your Selection') }}</h2>

                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-text-mint/70">{{ __('Subtotal') }}</dt>
                            <dd class="text-text-mint">€{{ number_format($totals['subtotal'], 2, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-mint/70">{{ __('Tax (18%)') }}</dt>
                            <dd class="text-text-mint">€{{ number_format($totals['tax'], 2, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-mint/70">{{ __('Shipping') }}</dt>
                            <dd class="text-text-mint">
                                @if ($totals['shipping'] > 0)
                                    €{{ number_format($totals['shipping'], 2, ',', '.') }}
                                @else
                                    <span class="text-accent-peach">{{ __('Free') }}</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <div class="hairline-gold my-6"></div>

                    <div class="flex justify-between items-baseline">
                        <span class="font-heading text-white uppercase tracking-wide text-sm">{{ __('Total') }}</span>
                        <span class="font-heading text-accent-gold text-3xl">€{{ number_format($totals['total'], 2, ',', '.') }}</span>
                    </div>

                    <p class="mt-6 font-accent italic text-sm text-text-mint/60 leading-relaxed">
                        {{ __('Final pricing is confirmed by our atelier upon review.') }}
                    </p>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function acquisitionWizard({ step, addresses, old, methods }) {
                const fallbackAddress = addresses.find((a) => a.is_default)?.id ?? (addresses[0]?.id ?? null);
                const oldAddress = old.address_id ? Number(old.address_id) : (old.submitted ? null : fallbackAddress);

                return {
                    step,
                    error: null,
                    submitting: false,
                    addresses,
                    methods,
                    selectedAddressId: addresses.length ? oldAddress : null,
                    form: {
                        full_name: old.full_name, phone: old.phone, street: old.street, city: old.city,
                        state: old.state, postal_code: old.postal_code, country: old.country,
                    },
                    preferredMethod: old.preferred_method,
                    note: old.customer_note ?? '',
                    acknowledged: old.acknowledged,

                    init() {
                        // A guard message disappears as soon as the client fixes what it asked for.
                        ['preferredMethod', 'acknowledged', 'form', 'selectedAddressId'].forEach((field) =>
                            this.$watch(field, () => { this.error = null; }));
                    },

                    toPreferences() {
                        this.error = null;

                        if (this.selectedAddressId === null) {
                            const missing = ['full_name', 'street', 'city', 'postal_code', 'country'].filter((f) => !String(this.form[f] ?? '').trim());
                            if (missing.length) {
                                this.error = 'Please complete your name, street, city, postal code and country.';
                                return;
                            }
                        }

                        this.step = 2;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },

                    toReview() {
                        this.error = null;

                        if (!this.preferredMethod) {
                            this.error = 'Please choose how you would prefer to settle.';
                            return;
                        }
                        if (!this.acknowledged) {
                            this.error = 'Please confirm you understand this is an acquisition request.';
                            return;
                        }

                        this.step = 3;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },

                    addressSummary() {
                        const a = this.selectedAddressId !== null
                            ? this.addresses.find((x) => x.id === this.selectedAddressId)
                            : this.form;

                        return a ? [a.full_name, a.street, a.city, a.postal_code, a.country].filter(Boolean).join(', ') : '—';
                    },
                };
            }
        </script>
    @endpush

</x-app-layout>
