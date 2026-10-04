{{-- Settlement instructions for an approved request (see App\Support\SettlementInstructions). --}}
@php($s = \App\Support\SettlementInstructions::for($order))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:28px 0; background:#1a2124; border:1px solid rgba(217,176,141,0.35);">
    <tr>
        <td style="padding:26px 28px;">
            <div style="font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:12px; letter-spacing:0.3em; text-transform:uppercase; color:#D9B08D;">Next step</div>
            <div style="margin-top:6px; font-family:'Playfair Display',Georgia,serif; font-size:20px; color:#ffffff;">{{ $s['title'] }}</div>
            <p style="margin:12px 0 18px; font-family:'Inter',Arial,sans-serif; font-size:14px; line-height:1.7; color:#D1E8E2;">{{ $s['intro'] }}</p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                @foreach ($s['details'] as $label => $value)
                    <tr>
                        <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-family:'Inter',Arial,sans-serif; font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(209,232,226,0.55); width:40%;" valign="top">{{ $label }}</td>
                        <td style="padding:8px 0; border-top:1px solid rgba(217,176,141,0.12); font-family:'Courier New',monospace; font-size:14px; color:#ffffff; word-break:break-all;">{{ $value }}</td>
                    </tr>
                @endforeach
            </table>

            @if ($s['note'])
                <p style="margin:16px 0 0; font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:15px; color:rgba(209,232,226,0.75);">{{ $s['note'] }}</p>
            @endif

            @if ($s['cta'])
                @include('emails.partials.button', ['url' => $s['cta']['url'], 'label' => $s['cta']['label']])
            @endif
        </td>
    </tr>
</table>
