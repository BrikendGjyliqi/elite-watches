<x-account-layout active="orders" :title="__('My Acquisitions')">

    <div class="space-y-4">
        @forelse ($orders as $order)
            @php
                $thumbs = $order->items->map(fn ($item) => $item->watch?->images->firstWhere('is_primary', true) ?? $item->watch?->images->first())->filter()->take(3);
            @endphp
            <a href="{{ route('account.orders.show', $order->order_number) }}" class="block border border-accent-gold/15 bg-primary-dark/60 p-5 sm:p-6 transition hover:border-accent-gold/40">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="flex -space-x-3 shrink-0" aria-hidden="true">
                        @forelse ($thumbs as $image)
                            <img src="{{ $image->path }}" alt="" class="h-14 w-14 object-cover border border-accent-gold/40 bg-primary-dark">
                        @empty
                            <span class="h-14 w-14 border border-accent-gold/30"></span>
                        @endforelse
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-accent italic text-accent-gold text-base">{{ $order->order_number }}</p>
                        <p class="text-xs text-text-mint/50 mt-1">
                            {{ $order->created_at->format('j F Y') }} &middot; {{ trans_choice(':count piece|:count pieces', $order->items->sum('quantity')) }}
                            @if ($order->methodLabel()) &middot; {{ $order->methodLabel() }} @endif
                        </p>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-5">
                        <x-status-pill :status="$order->status" />
                        <span class="font-heading text-accent-gold text-lg sm:w-32 text-right whitespace-nowrap">€{{ number_format((float) $order->total, 2, ',', '.') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="hidden sm:block h-4 w-4 text-text-mint/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>

                @if ($order->status === 'declined' && $order->declined_reason)
                    <p class="mt-4 pt-4 border-t border-[#E8A598]/20 font-accent italic text-[15px] text-[#E8A598]">
                        &ldquo;{{ $order->declined_reason }}&rdquo;
                    </p>
                @elseif ($order->status === 'under_review')
                    <p class="mt-4 pt-4 border-t border-accent-gold/15 text-sm text-accent-gold">{{ __('Our atelier has a question for you — open to reply.') }}</p>
                @endif
            </a>
        @empty
            <div class="text-center py-20 border border-accent-gold/10">
                <p class="font-heading text-2xl text-white mb-3">{{ __('No acquisitions yet') }}</p>
                <p class="font-accent italic text-lg text-text-mint/60 mb-8">{{ __('When you request a piece, it will appear here.') }}</p>
                <x-btn-primary :href="route('shop.index')" variant="outline">{{ __('Explore the collection') }}</x-btn-primary>
            </div>
        @endforelse
    </div>

    @if ($orders->hasPages())
        <div class="mt-10">
            {{ $orders->links() }}
        </div>
    @endif

</x-account-layout>
