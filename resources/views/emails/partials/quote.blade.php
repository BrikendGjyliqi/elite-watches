{{-- A message set apart as a quotation. $label optional. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td style="padding:22px 26px; background:#1a2124; border-left:2px solid #D9B08D;">
            @isset($label)
                <div style="font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:12px; letter-spacing:0.3em; text-transform:uppercase; color:#D9B08D; margin-bottom:8px;">{{ $label }}</div>
            @endisset
            <div style="font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:18px; line-height:1.6; color:#D1E8E2;">&ldquo;{!! nl2br(e($text)) !!}&rdquo;</div>
        </td>
    </tr>
</table>
