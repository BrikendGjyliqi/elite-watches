<x-account-layout active="orders" :title="__('Acquisition :number', ['number' => $order->order_number])">

    <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
        <a href="{{ route('account.orders') }}" class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-text-mint/60 hover:text-accent-gold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('My acquisitions') }}
        </a>
        <x-status-pill :status="$order->status" class="text-xs" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">

            @if ($order->status === 'declined')
                <section class="border border-[#E8A598]/40 bg-[#E8A598]/5 p-6">
                    <p class="font-accent italic text-[#E8A598] text-xs uppercase tracking-[0.3em] mb-3">{{ __('A word from our atelier') }}</p>
                    <p class="font-accent italic text-xl leading-relaxed text-text-mint">&ldquo;{{ $order->declined_reason }}&rdquo;</p>
                    <p class="mt-4 text-sm text-text-mint/60">{{ __('No payment was taken.') }} <a href="{{ route('shop.index') }}" class="text-accent-gold hover:underline underline-offset-4">{{ __('Explore other pieces') }}</a></p>
                </section>
            @endif

            {{-- Settlement instructions, once approved --}}
            @if ($order->showsSettlementInstructions())
                @php
                    $s = \App\Support\SettlementInstructions::for($order);
                @endphp
                <section class="border border-accent-gold/50 bg-accent-gold/[0.04] p-6 sm:p-8" aria-labelledby="settlement-heading">
                    <p class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em]">{{ __('Next step') }}</p>
                    <h2 id="settlement-heading" class="mt-2 font-heading text-white text-2xl">{{ $s['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-text-mint/80">{{ $s['intro'] }}</p>

                    <dl class="mt-6 divide-y divide-accent-gold/10 border-y border-accent-gold/10">
                        @foreach ($s['details'] as $label => $value)
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 py-3">
                                <dt class="text-[11px] uppercase tracking-[0.18em] text-text-mint/50">{{ $label }}</dt>
                                <dd class="sm:col-span-2 font-mono text-sm text-white break-all">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if ($s['note'])
                        <p class="mt-4 font-accent italic text-base text-text-mint/70">{{ $s['note'] }}</p>
                    @endif
                    @if ($s['cta'])
                        <div class="mt-6">
                            <x-btn-primary :href="$s['cta']['url']" variant="outline">{{ $s['cta']['label'] }}</x-btn-primary>
                        </div>
                    @endif
                </section>
            @endif

            <section aria-labelledby="items-heading">
                <h2 id="items-heading" class="font-heading text-white text-xl mb-6">{{ __('Pieces') }}</h2>
                <div class="divide-y divide-primary-teal/10 border-y border-primary-teal/10">
                    @foreach ($order->items as $item)
                        @php $primaryImage = $item->watch?->images->firstWhere('is_primary', true) ?? $item->watch?->images->first(); @endphp
                        <div class="flex items-center gap-4 py-4">
                            <div class="w-16 h-16 shrink-0 bg-primary-teal/10 overflow-hidden border border-accent-gold/30">
                                @if ($primaryImage)
                                    <img src="{{ $primaryImage->path }}" alt="{{ $item->watch->name }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.25em]">{{ $item->watch?->brand?->name }}</p>
                                <p class="text-white text-sm truncate">{{ $item->watch->name ?? __('Watch no longer available') }}</p>
                                <p class="text-xs text-text-mint/50">{{ __('Qty') }} {{ $item->quantity }} &times; €{{ number_format((float) $item->unit_price, 2, ',', '.') }}</p>
                            </div>
                            <p class="font-heading text-accent-gold shrink-0">€{{ number_format((float) $item->subtotal, 2, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($order->customer_note)
                <section>
                    <p class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em] mb-3">{{ __('Your note to our atelier') }}</p>
                    <blockquote class="border-l-2 border-accent-gold pl-5 font-accent italic text-lg leading-relaxed text-text-mint whitespace-pre-line">{{ $order->customer_note }}</blockquote>
                </section>
            @endif

            {{-- Conversation with the atelier --}}
            @if ($order->messages->isNotEmpty() || $order->isOpen())
                <section id="conversation" class="scroll-mt-28" aria-labelledby="conversation-heading">
                    <h2 id="conversation-heading" class="font-heading text-white text-xl mb-6">{{ __('Conversation with the atelier') }}</h2>

                    @if (session('status') === 'message-sent')
                        <p class="mb-6 border border-accent-gold/30 bg-accent-gold/5 px-5 py-3 text-sm text-accent-gold" role="status">{{ __('Your message has been sent to the atelier.') }}</p>
                    @endif

                    @if ($order->messages->isEmpty())
                        <p class="font-accent italic text-lg text-text-mint/50">{{ __('No messages yet. Our atelier will be in touch shortly.') }}</p>
                    @else
                        <ol class="space-y-4">
                            @foreach ($order->messages as $message)
                                <li @class([
                                    'max-w-[85%] p-5',
                                    'border border-accent-gold/30 bg-accent-gold/[0.05]' => $message->isFromAtelier(),
                                    'ml-auto border border-primary-teal/30 bg-primary-teal/10' => ! $message->isFromAtelier(),
                                ])>
                                    <p class="font-accent italic text-xs uppercase tracking-[0.25em] {{ $message->isFromAtelier() ? 'text-accent-gold' : 'text-text-mint/60' }}">
                                        {{ $message->isFromAtelier() ? __('The ÉLITE Atelier') : __('You') }}
                                        <span class="normal-case tracking-normal text-text-mint/40"> · {{ $message->created_at->timezone(config('app.timezone'))->format('j M · H:i') }}</span>
                                    </p>
                                    <p class="mt-2 text-sm leading-relaxed text-text-mint whitespace-pre-line">{{ $message->body }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    @if ($order->isOpen())
                        <form method="POST" action="{{ route('account.orders.reply', $order->order_number) }}" class="mt-8" x-data="{ body: @js(old('body', '')) }">
                            @csrf
                            <label for="reply-body" class="block font-accent italic text-accent-gold text-[11px] uppercase tracking-[0.3em] mb-2">{{ __('Write to the atelier') }}</label>
                            <textarea
                                id="reply-body"
                                name="body"
                                rows="3"
                                maxlength="2000"
                                x-model="body"
                                class="w-full bg-transparent border-0 border-b border-text-mint/20 focus:border-accent-gold px-0.5 py-3 text-sm text-white placeholder:text-text-mint/30 focus:outline-none focus:ring-0"
                                placeholder="{{ __('Your reply…') }}"
                            ></textarea>
                            @error('body')
                                <p class="mt-2 text-sm text-[#E8A598]">{{ $message }}</p>
                            @enderror
                            <div class="mt-4 flex justify-end">
                                <x-btn-primary type="submit" variant="outline">{{ __('Send message') }}</x-btn-primary>
                            </div>
                        </form>
                    @endif
                </section>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-8">
            <section class="border border-primary-teal/20 p-6" aria-labelledby="journey-heading">
                <h3 id="journey-heading" class="font-accent italic text-accent-gold text-xs uppercase tracking-[0.3em] mb-6">{{ __('Journey') }}</h3>
                <x-acquisition-timeline :order="$order" />
            </section>

            <div class="border border-primary-teal/20 p-6">
                <h3 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-4">{{ __('Summary') }}</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-text-mint/60">{{ __('Subtotal') }}</dt><dd class="text-text-mint">€{{ number_format((float) $order->subtotal, 2, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-text-mint/60">{{ __('Tax') }}</dt><dd class="text-text-mint">€{{ number_format((float) $order->tax, 2, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-text-mint/60">{{ __('Shipping') }}</dt><dd class="text-text-mint">{{ $order->shipping > 0 ? '€'.number_format((float) $order->shipping, 2, ',', '.') : __('Free') }}</dd></div>
                </dl>
                <div class="hairline-gold my-4"></div>
                <div class="flex justify-between items-baseline">
                    <span class="text-white uppercase text-xs tracking-widest">{{ __('Total') }}</span>
                    <span class="font-heading text-accent-gold text-2xl">€{{ number_format((float) $order->total, 2, ',', '.') }}</span>
                </div>
                @if ($order->original_total && (float) $order->original_total !== (float) $order->total)
                    <p class="mt-2 text-xs text-text-mint/50">{{ __('Adjusted by the atelier from') }} €{{ number_format((float) $order->original_total, 2, ',', '.') }}</p>
                @endif
                @if ($order->methodLabel())
                    <p class="mt-4 pt-4 border-t border-primary-teal/10 text-sm text-text-mint/70">{{ __('Settlement') }} · <span class="text-white">{{ $order->methodLabel() }}</span></p>
                @endif
            </div>

            @if ($order->shippingAddress)
                <div class="border border-primary-teal/20 p-6">
                    <h3 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-4">{{ __('Shipping Address') }}</h3>
                    <p class="text-sm text-text-mint">{{ $order->shippingAddress->full_name }}</p>
                    <p class="text-sm text-text-mint/70">{{ $order->shippingAddress->street }}</p>
                    <p class="text-sm text-text-mint/70">{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}</p>
                    <p class="text-sm text-text-mint/70">{{ $order->shippingAddress->country }}</p>
                </div>
            @endif
        </div>
    </div>

</x-account-layout>
