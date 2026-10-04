@extends('emails.layouts.maison')

@section('title', 'New acquisition request · '.$order->order_number)
@section('preheader', $order->user->name.' · €'.number_format((float) $order->total, 2, ',', '.').' · '.($order->methodLabel() ?? 'No preference'))
@section('eyebrow', 'New acquisition request')
@section('heading', $order->order_number)

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach ([
            'Client' => $order->user->name.' · '.$order->user->email,
            'Phone' => $order->shippingAddress?->phone ?: $order->user->phone ?: '—',
            'Preferred settlement' => $order->methodLabel() ?? '—',
            'Submitted' => $order->created_at->timezone(config('app.timezone'))->format('l j F Y · H:i'),
            'Respond by' => $order->created_at->addHours(config('concierge.sla_hours'))->timezone(config('app.timezone'))->format('l j F · H:i'),
        ] as $label => $value)
            <tr>
                <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(209,232,226,0.55); width:38%;" valign="top">{{ $label }}</td>
                <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-size:14px; color:#ffffff;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    @include('emails.partials.items', ['order' => $order])

    @if ($order->customer_note)
        @include('emails.partials.quote', ['label' => 'Client note', 'text' => $order->customer_note])
    @endif

    @include('emails.partials.button', ['url' => $reviewUrl, 'label' => 'Review now'])
@endsection
