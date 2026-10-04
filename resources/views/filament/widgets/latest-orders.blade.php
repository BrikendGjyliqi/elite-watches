<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">Latest orders</p>
            <a href="{{ $indexUrl }}" class="elite-tile__link">View all →</a>
        </header>

        @if ($orders->isEmpty())
            <x-elite.empty>No orders yet · The vault awaits its first keeper.</x-elite.empty>
        @else
            <div class="-mx-3 overflow-x-auto">
                <table class="elite-table">
                    <thead>
                        <tr>
                            <th scope="col">Order</th>
                            <th scope="col">Customer</th>
                            <th scope="col" class="text-right">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $url = $urlFor($order);
                            @endphp
                            <tr x-on:click="if (! $event.target.closest('a')) window.location.href = @js($url)">
                                <td>
                                    <a href="{{ $url }}" class="elite-italic elite-gold" style="font-size: 15px; white-space: nowrap">{{ $order->order_number }}</a>
                                </td>
                                <td class="truncate" style="max-width: 130px">{{ $order->user?->name ?? 'Guest' }}</td>
                                <td class="text-right elite-accent" style="font-size: 15px; white-space: nowrap">€{{ number_format((float) $order->total, 2) }}</td>
                                <td><span class="elite-pill is-{{ $order->status }}">{{ $order->status }}</span></td>
                                <td class="text-right elite-italic" style="color: var(--elite-mint); white-space: nowrap; font-size: 14px">{{ $order->created_at->format('j M') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </article>
</x-filament-widgets::widget>
