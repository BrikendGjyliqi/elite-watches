<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">
                Pending acquisitions · Awaiting your review
                @if ($openCount > 0)
                    <span class="elite-count-badge">{{ $openCount }}</span>
                @endif
            </p>
            <a href="{{ $indexUrl }}" class="elite-tile__link">Review all →</a>
        </header>

        @if ($orders->isEmpty())
            <x-elite.empty icon="crown">No pending acquisitions · the vault is quiet.</x-elite.empty>
        @else
            <div class="elite-list flex flex-col">
                @foreach ($orders as $order)
                    @php
                        $urgency = $urgency($order);
                        $thumb = \App\Support\WatchImageUrl::primary($order->items->first()?->watch);
                    @endphp
                    <a href="{{ $reviewUrl($order) }}" class="elite-row-link flex flex-wrap sm:flex-nowrap items-center gap-4 py-3">
                        @if ($thumb)
                            <img src="{{ $thumb }}" alt="" class="elite-thumb" style="width: 48px; height: 48px" loading="lazy">
                        @else
                            <span class="elite-thumb" style="width: 48px; height: 48px" aria-hidden="true"></span>
                        @endif

                        <span class="min-w-0 flex-1">
                            <span class="elite-italic elite-gold block" style="font-size: 15px">{{ $order->order_number }}</span>
                            <span class="block truncate" style="font-size: 14px; color: #fff">{{ $order->user?->name }}</span>
                            @if ($order->isBeingReviewedByAnotherAdmin(auth()->id()))
                                <span class="block elite-italic" style="font-size: 13px; color: var(--elite-gold)">Being reviewed by {{ str($order->reviewingAdmin?->name)->before(' ') }}</span>
                            @endif
                        </span>

                        <span class="hidden md:block elite-muted" style="font-size: 12px; white-space: nowrap">{{ $order->methodLabel() ? config("concierge.methods.{$order->preferred_method}.short") : '—' }}</span>
                        <span class="elite-accent" style="font-size: 16px; white-space: nowrap">€{{ number_format((float) $order->total, 2) }}</span>
                        <span class="elite-sla elite-sla--{{ $urgency }}" title="Submitted {{ $order->created_at->timezone(config('app.timezone'))->format('j M · H:i') }}">
                            {{ $order->created_at->diffForHumans(short: true, syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </article>
</x-filament-widgets::widget>
