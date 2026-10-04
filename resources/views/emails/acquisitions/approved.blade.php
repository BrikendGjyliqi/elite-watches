@extends('emails.layouts.maison')

@section('title', 'Your acquisition has been approved · ÉLITE')
@section('preheader', 'Your piece is reserved. Here is how to complete your acquisition.')
@section('eyebrow', 'Acquisition '.$order->order_number)
@section('heading', 'Your acquisition has been approved.')

@section('content')
    <p style="margin:0 0 16px;">Dear {{ $order->user->name }},</p>
    <p style="margin:0;">
        We are delighted to confirm your request. Your piece has been reserved in your name,
        and the details to complete your acquisition are below.
    </p>

    @if ($order->admin_response)
        @include('emails.partials.quote', ['label' => 'A personal note from our atelier', 'text' => $order->admin_response])
    @endif

    @include('emails.partials.settlement', ['order' => $order])

    @include('emails.partials.items', ['order' => $order])

    @include('emails.partials.button', ['url' => route('account.orders.show', $order->order_number), 'label' => 'View your acquisition'])
@endsection
