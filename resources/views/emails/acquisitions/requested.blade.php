@extends('emails.layouts.maison')

@section('title', "We've received your request · ÉLITE")
@section('preheader', 'Your acquisition request '.$order->order_number.' is with our atelier.')
@section('eyebrow', 'Acquisition '.$order->order_number)
@section('heading', 'Request received.')

@section('content')
    <p style="margin:0 0 16px;">Dear {{ $order->user->name }},</p>
    <p style="margin:0 0 16px;">
        Thank you for your request. A member of our atelier will contact you within {{ config('concierge.sla_hours') }} hours
        to confirm availability, finalise pricing and arrange settlement
        @if ($order->methodLabel()) by your preferred method — <span style="color:#D9B08D;">{{ $order->methodLabel() }}</span>@endif.
    </p>
    <p style="margin:0;">No payment is taken until your request has been approved.</p>

    @include('emails.partials.items', ['order' => $order])

    @if ($order->customer_note)
        @include('emails.partials.quote', ['label' => 'Your note to our atelier', 'text' => $order->customer_note])
    @endif

    @include('emails.partials.button', ['url' => route('account.orders.show', $order->order_number), 'label' => 'View your request', 'variant' => 'outline'])
@endsection
