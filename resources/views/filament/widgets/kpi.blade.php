@php
    $trend = $kpi['trend'];
    $decimals = $kpi['money'] ? 2 : 0;
    // For durations a fall is good news, so the colours flip.
    $improving = $trend !== null && (($kpi['lowerIsBetter'] ?? false) ? $trend <= 0 : $trend >= 0);
@endphp

<x-filament-widgets::widget>
    <article class="elite-tile elite-kpi">
        <div class="elite-kpi__top">
            <span class="elite-kpi__icon">
                <x-filament::icon :icon="$kpi['icon']" />
            </span>

            @if ($trend === null)
                <span class="elite-trend elite-trend--flat" title="Nothing to compare yet">—</span>
            @else
                <span
                    @class(['elite-trend', 'elite-trend--up' => $improving, 'elite-trend--down' => ! $improving])
                    title="Against the previous period"
                >
                    {{ $trend >= 0 ? '▲' : '▼' }} {{ number_format(abs($trend), 1) }}%
                </span>
            @endif
        </div>

        <p class="elite-eyebrow elite-kpi__label">{{ $kpi['label'] }}</p>

        @isset($kpi['display'])
            <p class="elite-kpi__value elite-kpi__value--gold">{{ $kpi['display'] }}</p>
        @else
            <p
                class="elite-kpi__value"
                x-data="{
                    target: {{ (float) $kpi['value'] }},
                    shown: @js(number_format((float) $kpi['value'], $decimals)),
                    format(n) {
                        return new Intl.NumberFormat('en-US', { minimumFractionDigits: {{ $decimals }}, maximumFractionDigits: {{ $decimals }} }).format(n);
                    },
                    run() {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || this.target === 0) return;
                        const start = performance.now();
                        const step = (t) => {
                            const p = Math.min((t - start) / 800, 1);
                            this.shown = this.format(this.target * (1 - Math.pow(1 - p, 3)));
                            if (p < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    },
                }"
                x-init="run()"
            >
                @if ($kpi['money'])<span class="elite-kpi__currency">€</span>@endif<span x-text="shown">{{ number_format((float) $kpi['value'], $decimals) }}</span>
            </p>
        @endisset

        <p class="elite-kpi__sub">{{ $kpi['sub'] }}</p>
    </article>
</x-filament-widgets::widget>
