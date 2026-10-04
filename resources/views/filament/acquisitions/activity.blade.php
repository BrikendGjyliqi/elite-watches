@php
    $order = $getRecord();
    $tz = config('app.timezone');

    $entries = \Spatie\Activitylog\Models\Activity::query()
        ->forSubject($order)
        ->with('causer')
        ->latest()
        ->limit(15)
        ->get()
        ->map(function ($activity) {
            $status = $activity->properties['attributes']['status'] ?? null;

            $what = match (true) {
                $activity->event === 'viewed' => 'opened the dossier',
                $activity->event === 'created' => 'submitted the request',
                $status !== null => 'set status to '.(\App\Models\Order::STATUS_LABELS[$status] ?? $status),
                default => 'updated '.collect($activity->properties['attributes'] ?? [])->keys()->map(fn ($k) => str_replace('_', ' ', $k))->join(', '),
            };

            return ['who' => $activity->causer?->name ?? 'System', 'what' => $what, 'when' => $activity->created_at];
        })
        // Collapse runs of repeated "opened the dossier" by the same person.
        ->reduce(function ($carry, $entry) {
            $last = $carry->last();
            if ($last && $last['who'] === $entry['who'] && $last['what'] === $entry['what'] && $entry['what'] === 'opened the dossier') {
                return $carry;
            }

            return $carry->push($entry);
        }, collect());
@endphp

@if ($entries->isEmpty())
    <p class="elite-italic elite-muted" style="font-size: 15px">No activity recorded yet.</p>
@else
    <ol class="elite-activity">
        @foreach ($entries as $entry)
            <li>
                <span class="elite-avatar elite-avatar--sm" aria-hidden="true">{{ \App\Filament\AvatarProviders\InitialsAvatarProvider::initials($entry['who']) }}</span>
                <span class="min-w-0">
                    <span class="block" style="font-size: 13px; color: var(--elite-mint)"><span class="text-white">{{ $entry['who'] }}</span> {{ $entry['what'] }}</span>
                    <time class="elite-italic elite-muted" style="font-size: 13px" datetime="{{ $entry['when']->toIso8601String() }}" title="{{ $entry['when']->timezone($tz)->format('j M Y · H:i') }}">{{ $entry['when']->diffForHumans() }}</time>
                </span>
            </li>
        @endforeach
    </ol>
@endif
