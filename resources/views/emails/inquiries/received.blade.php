@extends('emails.layouts.maison')

@section('title', 'New inquiry · '.$inquiry->reference())
@section('preheader', $inquiry->name.' · '.$inquiry->subjectLabel().' · prefers '.$inquiry->channelLabel())
@section('eyebrow', 'New inquiry · '.$inquiry->reference())
@section('heading', $inquiry->subjectLabel())

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach ([
            'From' => $inquiry->name,
            'Email' => $inquiry->email,
            'Phone' => $inquiry->phone ?: '—',
            'Prefers a reply by' => $inquiry->channelLabel(),
            'Received' => $inquiry->created_at->timezone(config('app.timezone'))->format('l j F Y · H:i'),
        ] as $label => $value)
            <tr>
                <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(209,232,226,0.55); width:38%;" valign="top">{{ $label }}</td>
                <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-size:14px; color:#ffffff;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    @include('emails.partials.quote', ['label' => 'Message', 'text' => $inquiry->message])

    @include('emails.partials.button', [
        'url' => 'mailto:'.$inquiry->email.'?subject='.rawurlencode('Re: '.$inquiry->subjectLabel().' · ÉLITE ('.$inquiry->reference().')'),
        'label' => 'Reply directly',
    ])

    <p style="margin:8px 0 0; text-align:center; font-size:12px; color:rgba(209,232,226,0.5);">
        Or reply from the admin panel, where the conversation is recorded:
        <a href="{{ \App\Filament\Resources\ContactInquiryResource::getUrl('index', panel: 'admin') }}" style="color:#D9B08D;">open inquiries</a>
    </p>
@endsection
