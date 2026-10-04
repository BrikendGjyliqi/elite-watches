<x-app-layout>

    <div class="bg-primary-dark min-h-screen pb-24">
        {{-- Hero band --}}
        <section class="relative overflow-hidden border-b border-accent-gold/40 bg-gradient-to-br from-primary-dark via-[#1f4a49] to-primary-teal">
            <div class="grain-overlay"></div>
            <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 text-center">
                <p class="font-accent italic text-accent-gold text-sm uppercase tracking-[0.3em]">{{ __('Acquisition') }} #{{ $order->order_number }}</p>
                <h1 class="mt-5 font-heading text-white text-5xl sm:text-6xl">{{ __('Request received.') }}</h1>
                <div class="hairline-gold w-24 mx-auto mt-8"></div>
                <p class="mt-8 mx-auto max-w-xl text-[15px] leading-relaxed text-text-mint">
                    {{ __('Our atelier has received your request. A member of our team will reach out within :hours hours to confirm availability, finalize pricing, and arrange settlement according to your preference.', ['hours' => config('concierge.sla_hours')]) }}
                </p>
            </div>
        </section>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-10 mt-16">
                {{-- Timeline --}}
                <section class="md:col-span-2" aria-labelledby="journey-heading">
                    <h2 id="journey-heading" class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em] mb-6">{{ __('Your acquisition') }}</h2>
                    <x-acquisition-timeline :order="$order" />
                </section>

                {{-- Request summary --}}
                <section class="md:col-span-3 border border-primary-teal/20 p-6" aria-labelledby="summary-heading">
                    <h2 id="summary-heading" class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em] mb-4">{{ __('Your request') }}</h2>
                    <div class="divide-y divide-primary-teal/10">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between gap-4 py-3 text-sm">
                                <span class="min-w-0">
                                    <span class="block font-accent italic text-accent-gold text-xs uppercase tracking-[0.25em]">{{ $item->watch?->brand?->name }}</span>
                                    <span class="text-white">{{ $item->watch->name ?? __('Watch no longer available') }}</span>
                                    <span class="text-text-mint/50"> &times; {{ $item->quantity }}</span>
                                </span>
                                <span class="font-heading text-accent-gold whitespace-nowrap">€{{ number_format((float) $item->subtotal, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="hairline-gold my-4"></div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-white uppercase text-xs tracking-widest">{{ __('Total') }}</span>
                        <span class="font-heading text-accent-gold text-2xl">€{{ number_format((float) $order->total, 2, ',', '.') }}</span>
                    </div>
                    @if ($order->methodLabel())
                        <p class="mt-4 text-sm text-text-mint/70">{{ __('Preferred settlement') }} · <span class="text-white">{{ $order->methodLabel() }}</span></p>
                    @endif
                </section>
            </div>

            {{-- What happens next --}}
            <section class="mt-16" aria-labelledby="next-heading">
                <h2 id="next-heading" class="text-center font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em] mb-8">{{ __('What happens next') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ([
                        ['M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z', __('Expect contact within :hours h', ['hours' => config('concierge.sla_hours')]), __('A member of our atelier will call or write to you personally.')],
                        ['M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', __('Verification of availability'), __('We confirm the piece, its condition and final pricing.')],
                        ['M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z', __('Secure settlement'), __('Completed via your chosen method — nothing is charged until then.')],
                    ] as [$icon, $title, $body])
                        <div class="border border-accent-gold/15 bg-primary-dark/60 p-6">
                            <svg class="h-6 w-6 text-accent-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                            <p class="mt-4 font-heading text-white">{{ $title }}</p>
                            <p class="mt-2 text-[13px] leading-relaxed text-text-mint/70">{{ $body }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="mt-16 flex flex-col sm:flex-row items-center justify-center gap-4">
                <x-btn-primary :href="route('account.orders')" variant="outline">{{ __('View my requests') }}</x-btn-primary>
                <x-btn-primary :href="route('shop.index')" variant="ghost">{{ __('Return to the boutique') }}</x-btn-primary>
            </div>
        </div>
    </div>

</x-app-layout>
