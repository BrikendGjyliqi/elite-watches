@php
    /** @var \App\Models\Order $order */
    $order = $getRecord()->loadMissing('reviewer');
    $tz = config('app.timezone');
@endphp

<div class="elite-dossier__card">
    <span class="elite-pill is-{{ $order->status }}">{{ $order->statusLabel() }}</span>

    <dl class="elite-totals mt-4">
        @if ($order->reviewer)
            <div><dt>Reviewed by</dt><dd>{{ $order->reviewer->name }}</dd></div>
        @endif
        @if ($order->approved_at)
            <div><dt>Approved</dt><dd>{{ $order->approved_at->timezone($tz)->format('j M Y · H:i') }}</dd></div>
            <div><dt>Time to approve</dt><dd>{{ $order->created_at->diffForHumans($order->approved_at, syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE, parts: 2) }}</dd></div>
        @endif
        @if ($order->declined_at)
            <div><dt>Declined</dt><dd>{{ $order->declined_at->timezone($tz)->format('j M Y · H:i') }}</dd></div>
        @endif
        @if ($order->methodLabel())
            <div><dt>Settlement</dt><dd>{{ $order->methodLabel() }}</dd></div>
        @endif
    </dl>

    @if ($order->declined_reason)
        <p class="elite-eyebrow mt-5">Reason given</p>
        <p class="elite-dossier__quote-text" style="margin-top: 6px">{{ $order->declined_reason }}</p>
    @endif

    @if ($order->admin_response && $order->status !== 'declined')
        <p class="elite-eyebrow mt-5">Message sent to client</p>
        <p class="elite-dossier__quote-text" style="margin-top: 6px">{{ $order->admin_response }}</p>
    @endif

    @if ($order->notes)
        <p class="elite-eyebrow mt-5">Internal note</p>
        <p style="margin-top: 6px; font-size: 13px; color: var(--elite-mint)">{{ $order->notes }}</p>
    @endif

    <p class="mt-5 elite-muted" style="font-size: 12px">
        Later stages (awaiting payment, paid, shipped, delivered) are set from
        <a class="elite-gold hover:underline" href="{{ \App\Filament\Resources\OrderResource::getUrl('view', ['record' => $order]) }}">the order record</a>.
    </p>
</div>
