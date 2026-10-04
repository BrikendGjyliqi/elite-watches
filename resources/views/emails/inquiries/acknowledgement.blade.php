@extends('emails.layouts.maison')

@section('title', "We've received your message · ÉLITE")
@section('preheader', 'Expect a reply within '.config('concierge.sla_hours').' hours.')
@section('eyebrow', 'Reference '.$inquiry->reference())
@section('heading', 'Your message has reached the atelier.')

@section('content')
    <p style="margin:0 0 6px; text-align:center; font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:19px; color:#D9B08D;">
        Expect a reply within {{ config('concierge.sla_hours') }} hours
    </p>

    <p style="margin:22px 0 16px;">Dear {{ $inquiry->name }},</p>
    <p style="margin:0;">
        Thank you for writing to ÉLITE. A member of our atelier will read your message personally and reply
        @if ($inquiry->preferred_channel === 'email')
            by email.
        @else
            {{-- "by phone", but "by WhatsApp": keep the brand's capitalisation --}}
            by {{ $inquiry->preferred_channel === 'phone' ? 'phone' : $inquiry->channelLabel() }} on {{ $inquiry->phone }}, as you asked.
        @endif
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
        @foreach ([
            'Regarding' => $inquiry->subjectLabel(),
            'Reply by' => $inquiry->channelLabel(),
            'Reference' => $inquiry->reference(),
        ] as $label => $value)
            <tr>
                <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(209,232,226,0.55); width:38%;">{{ $label }}</td>
                <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-size:14px; color:#ffffff;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    @include('emails.partials.quote', ['label' => 'Your message', 'text' => $inquiry->message])

    <p style="margin:0; font-size:13px; color:rgba(209,232,226,0.6);">
        Need to add something? Simply reply to this email.
    </p>
@endsection
