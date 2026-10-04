@extends('emails.layouts.maison')

@section('title', 'Regarding your recent request · ÉLITE')
@section('preheader', 'An update on your acquisition request '.$order->order_number.'.')
@section('eyebrow', 'Acquisition '.$order->order_number)
@section('heading', 'Regarding your recent request.')

@section('content')
    <p style="margin:0 0 16px;">Dear {{ $order->user->name }},</p>
    <p style="margin:0;">
        Thank you for the trust you placed in our maison. After careful consideration, we are unable
        to fulfil this request on this occasion.
    </p>

    @include('emails.partials.quote', ['label' => 'From our atelier', 'text' => $order->declined_reason])

    <p style="margin:0;">No payment has been taken. Should you wish to discuss this further, simply reply to this email.</p>

    @if ($alternatives->isNotEmpty())
        <div style="margin:32px 0 6px; font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:12px; letter-spacing:0.3em; text-transform:uppercase; color:#D9B08D;">Pieces you may also admire</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            @foreach ($alternatives as $watch)
                <tr>
                    <td style="padding:14px 0; border-bottom:1px solid rgba(217,176,141,0.12);">
                        <a href="{{ route('shop.show', $watch->slug) }}" style="text-decoration:none;">
                            <div style="font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:12px; letter-spacing:0.25em; text-transform:uppercase; color:#D9B08D;">{{ $watch->brand?->name }}</div>
                            <div style="font-family:'Playfair Display',Georgia,serif; font-size:16px; color:#ffffff;">{{ $watch->name }}</div>
                        </a>
                    </td>
                    <td align="right" style="padding:14px 0; border-bottom:1px solid rgba(217,176,141,0.12); font-family:'Playfair Display',Georgia,serif; font-size:15px; color:#D9B08D; white-space:nowrap;">
                        €{{ number_format((float) ($watch->discount_price ?? $watch->price), 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>
        @include('emails.partials.button', ['url' => route('shop.index'), 'label' => 'Explore the collection', 'variant' => 'outline'])
    @endif
@endsection
