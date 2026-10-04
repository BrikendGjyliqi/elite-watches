@php
    $tz = config('app.timezone');
    $now = now($tz);
@endphp

<x-filament-panels::page class="fi-dashboard-page elite-dashboard">
    <section class="elite-dash-hero" aria-label="Welcome">
        <div>
            <p class="elite-dash-hero__eyebrow">{{ mb_strtoupper($now->format('l · d F Y')) }}</p>
            <h1 class="elite-dash-hero__headline">{{ $this->getGreeting() }}, {{ $this->getFirstName() }}.</h1>
            <p class="elite-dash-hero__subhead">Welcome back to the maison.</p>
        </div>

        <div
            x-data="{
                tz: @js($tz),
                hm: @js($now->format('H:i')),
                s: @js($now->format('s')),
                tick() {
                    const parts = new Intl.DateTimeFormat('en-GB', {
                        timeZone: this.tz, hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
                    }).formatToParts(new Date());
                    const p = (type) => parts.find((part) => part.type === type)?.value;
                    this.hm = `${p('hour')}:${p('minute')}`;
                    this.s = p('second');
                },
                init() {
                    this.tick();
                    this.timer = setInterval(() => this.tick(), 1000);
                },
                destroy() {
                    clearInterval(this.timer);
                },
            }"
        >
            <p class="elite-dash-hero__clock" role="timer" aria-live="off">
                <span x-text="hm">{{ $now->format('H:i') }}</span><span class="elite-dash-hero__clock-seconds" x-text="s">{{ $now->format('s') }}</span>
            </p>
            <p class="elite-dash-hero__weather">6°C · Clear · Prishtina</p>
        </div>
    </section>

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="$this->getWidgetData()"
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
