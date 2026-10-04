@extends('emails.layouts.maison')

@section('title', 'A reply from the atelier · ÉLITE')
@section('preheader', 'Regarding your message '.$inquiry->reference().'.')
@section('eyebrow', 'Reference '.$inquiry->reference())
@section('heading', 'A reply from the atelier.')

@section('content')
    <p style="margin:0 0 16px;">Dear {{ $inquiry->name }},</p>

    <div style="font-size:15px; line-height:1.8; color:#D1E8E2;">{!! nl2br(e($inquiry->reply)) !!}</div>

    @include('emails.partials.quote', ['label' => 'You wrote', 'text' => str($inquiry->message)->limit(600)->toString()])

    <p style="margin:0; font-size:13px; color:rgba(209,232,226,0.6);">You can reply to this email at any time.</p>
@endsection
