@extends('emails.layouts.maison')

@section('title', 'A note from our atelier · ÉLITE')
@section('preheader', 'Our atelier has a question about your request '.$order->order_number.'.')
@section('eyebrow', 'Acquisition '.$order->order_number)
@section('heading', 'A note from our atelier.')

@section('content')
    <p style="margin:0 0 16px;">Dear {{ $order->user->name }},</p>
    <p style="margin:0;">While preparing your request, our atelier would like to ask you the following:</p>

    @include('emails.partials.quote', ['text' => $question])

    <p style="margin:0;">
        You can reply directly from your request page, and your answer will reach the atelier straight away.
    </p>

    @include('emails.partials.button', ['url' => route('account.orders.show', $order->order_number).'#conversation', 'label' => 'Reply to the atelier'])
@endsection
