@php
    $tz = config('app.timezone');
    $now = now($tz);
@endphp

<div
    class="elite-topbar-clock hidden md:flex"
    x-data="{
        tz: @js($tz),
        abbr: @js($now->format('T')),
        label: @js($now->format('l · j F Y · H:i T')),
        tick() {
            const now = new Date();
            const parts = new Intl.DateTimeFormat('en-GB', {
                timeZone: this.tz, weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: false,
            }).formatToParts(now);
            const p = (type) => parts.find((part) => part.type === type)?.value;
            this.label = `${p('weekday')} · ${p('day')} ${p('month')} ${p('year')} · ${p('hour')}:${p('minute')} ${this.abbr}`;
        },
    }"
    x-init="tick(); setInterval(() => tick(), 15000)"
>
    <time datetime="{{ $now->toIso8601String() }}" x-text="label">{{ $now->format('l · j F Y · H:i T') }}</time>
    <span class="elite-topbar-divider" aria-hidden="true"></span>
</div>
