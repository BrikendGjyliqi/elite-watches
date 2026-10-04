<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">Order status</p>
            <span class="elite-italic elite-muted" style="font-size: 13px">{{ number_format($total) }} {{ str('order')->plural($total) }}</span>
        </header>

        @if ($total === 0)
            <div class="elite-stack" aria-hidden="true"></div>
            <p class="elite-italic elite-muted" style="margin-top: 14px; font-size: 15px">No orders yet · The vault awaits its first keeper.</p>
        @else
            <div class="elite-stack" role="img" aria-label="Order status distribution">
                @foreach ($statuses as $status)
                    @if ($status['count'] > 0)
                        <span class="is-{{ $status['key'] }}" style="width: {{ $status['percent'] }}%; background: var(--elite-status)" title="{{ $status['label'] }} · {{ $status['percent'] }}%"></span>
                    @endif
                @endforeach
            </div>
        @endif

        <ul class="elite-status-list" style="margin-top: 16px">
            @foreach ($statuses as $status)
                <li>
                    <span class="elite-dot is-{{ $status['key'] }}" style="background: var(--elite-status)"></span>
                    <span class="flex-1">{{ $status['label'] }}</span>
                    <span class="elite-num" style="font-size: 15px">{{ number_format($status['count']) }}</span>
                    <span class="elite-muted text-right" style="width: 52px; font-variant-numeric: tabular-nums">{{ number_format($status['percent'], 1) }}%</span>
                </li>
            @endforeach
        </ul>
    </article>
</x-filament-widgets::widget>
