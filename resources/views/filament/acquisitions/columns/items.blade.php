@php
    $items = $getRecord()->items;
    $shown = $items->take(3);
    $more = $items->count() - $shown->count();
@endphp

<div class="elite-strip" title="{{ $items->map(fn ($item) => $item->quantity.' × '.($item->watch?->name ?? 'Unavailable piece'))->join("\n") }}">
    @foreach ($shown as $item)
        @php
            $url = \App\Support\WatchImageUrl::primary($item->watch);
        @endphp
        @if ($url)
            <img src="{{ $url }}" alt="{{ $item->watch?->name }}" class="elite-strip__thumb" loading="lazy" width="40" height="40">
        @else
            <span class="elite-strip__thumb" aria-hidden="true"></span>
        @endif
    @endforeach
    @if ($more > 0)
        <span class="elite-strip__more">+{{ $more }} more</span>
    @endif
</div>
