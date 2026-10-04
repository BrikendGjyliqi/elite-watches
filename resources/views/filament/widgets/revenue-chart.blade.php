@php
    $total = $this->getRangeTotal();
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <article class="elite-tile">
        <header class="elite-tile__head" style="align-items: flex-start">
            <div>
                <p class="elite-eyebrow">Revenue · {{ $this->getRangeLabel() }}</p>
                <p class="elite-chart__total"><span>€</span>{{ number_format($total, 2) }}</p>
            </div>

            <div class="elite-tabs" role="tablist" aria-label="Revenue range">
                @foreach (\App\Filament\Widgets\RevenueChartWidget::RANGES as $key => $range)
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $filter === $key ? 'true' : 'false' }}"
                        @class(['elite-tab', 'is-active' => $filter === $key])
                        wire:click="setRange('{{ $key }}')"
                        title="{{ $range['label'] }}"
                    >
                        {{ strtoupper($key) }}
                    </button>
                @endforeach
            </div>
        </header>

        <div class="relative">
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                x-data="chart({
                    cachedData: @js($this->getCachedData()),
                    options: @js($this->getOptions()),
                    type: @js($this->getType()),
                })"
                style="height: 280px"
            >
                <canvas x-ref="canvas" style="max-height: 280px"></canvas>

                {{-- Colour probes read by Filament's chart component. --}}
                <span x-ref="backgroundColorElement" style="color: rgba(217, 176, 141, 0.15)"></span>
                <span x-ref="borderColorElement" style="color: #D9B08D"></span>
                <span x-ref="gridColorElement" style="color: rgba(209, 232, 226, 0.05)"></span>
                <span x-ref="textColorElement" style="color: rgba(209, 232, 226, 0.5)"></span>
            </div>

            @if ($total <= 0)
                <div class="elite-empty absolute inset-0 pointer-events-none" style="padding-bottom: 48px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
                    No revenue in this period · The ledger awaits its first entry.
                </div>
            @endif
        </div>
    </article>
</x-filament-widgets::widget>
