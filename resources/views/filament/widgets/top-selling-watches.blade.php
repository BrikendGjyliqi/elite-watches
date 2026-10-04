<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">Bestsellers · This month</p>
        </header>

        @if ($watches->isEmpty())
            <x-elite.empty icon="crown">No pieces sold this month · The collection awaits its admirers.</x-elite.empty>
        @else
            <div class="elite-list flex flex-col">
                @foreach ($watches as $watch)
                    @php
                        $image = $imageFor($watch);
                    @endphp
                    <a href="{{ $urlFor($watch) }}" class="elite-row-link flex items-center gap-4 py-3">
                        @if ($image)
                            <img src="{{ $image }}" alt="" class="elite-thumb" loading="lazy" width="64" height="64">
                        @else
                            <span class="elite-thumb grid place-items-center elite-gold" aria-hidden="true">
                                <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6" />
                            </span>
                        @endif
                        <span class="min-w-0 flex-1">
                            <span class="elite-eyebrow block">{{ $watch->brand?->name }}</span>
                            <span class="block truncate" style="font-family: var(--elite-serif); font-size: 15px; color: #fff">{{ $watch->name }}</span>
                            <span class="elite-accent block" style="font-size: 14px">€{{ number_format((float) ($watch->discount_price ?? $watch->price), 2) }}</span>
                        </span>
                        <span class="elite-badge-mint">{{ (int) $watch->units_sold }} sold</span>
                    </a>
                @endforeach
            </div>
        @endif
    </article>
</x-filament-widgets::widget>
