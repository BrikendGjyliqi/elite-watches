<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">Activity</p>
            <a href="{{ $auditUrl }}" class="elite-tile__link">Audit log →</a>
        </header>

        @if ($activities->isEmpty())
            <x-elite.empty>The ledger is quiet · Admin actions will be recorded here.</x-elite.empty>
        @else
            <ol class="elite-list">
                @foreach ($activities as $activity)
                    <li class="py-3 first:pt-0" style="font-size: 13px; line-height: 1.5">
                        <p style="color: var(--elite-mint)">{{ $describe($activity) }}</p>
                        <time
                            class="elite-italic"
                            style="font-size: 13px; color: rgba(209, 232, 226, 0.5)"
                            datetime="{{ $activity->created_at->toIso8601String() }}"
                            title="{{ $activity->created_at->format('j M Y · H:i') }}"
                        >{{ $activity->created_at->diffForHumans() }}</time>
                    </li>
                @endforeach
            </ol>
        @endif
    </article>
</x-filament-widgets::widget>
