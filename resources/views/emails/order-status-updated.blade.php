@extends('emails.layouts.maison')

@section('title', 'An update on your acquisition · ÉLITE')
@section('preheader', 'Your acquisition '.$order->order_number.' is now '.strtolower($order->statusLabel()).'.')
@section('eyebrow', 'Acquisition '.$order->order_number)
@section('heading', $order->statusLabel().'.')

@section('content')
    <p style="margin:0 0 16px;">Dear {{ $order->user->name }},</p>
    <p style="margin:0;">
        @switch($order->status)
            @case('awaiting_payment') Your piece is reserved and we are awaiting your settlement. @break
            @case('paid') We have received your settlement — thank you. Your piece is now being prepared. @break
            @case('shipped') Your piece is on its way to you, insured and tracked. @break
            @case('delivered') Your piece has been delivered. We hope it brings you many years of joy. @break
            @case('cancelled') This acquisition has been cancelled. If this is unexpected, simply reply to this email. @break
            @default Your acquisition is now <span style="color:#D9B08D;">{{ strtolower($order->statusLabel()) }}</span>.
        @endswitch
    </p>

    @if ($order->showsSettlementInstructions())
        @include('emails.partials.settlement', ['order' => $order])
    @endif

    @include('emails.partials.button', ['url' => route('account.orders.show', $order->order_number), 'label' => 'View your acquisition', 'variant' => 'outline'])
@endsection
