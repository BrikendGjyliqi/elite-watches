{{-- Gold call-to-action. $variant: 'filled' (default) or 'outline'. --}}
@php($filled = ($variant ?? 'filled') === 'filled')
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px auto 8px;">
    <tr>
        <td align="center" style="background:{{ $filled ? '#D9B08D' : 'transparent' }}; border:1px solid #D9B08D;">
            <a href="{{ $url }}" target="_blank" rel="noopener"
               style="display:inline-block; padding:15px 34px; font-family:'Inter','Helvetica Neue',Arial,sans-serif; font-size:12px; font-weight:500; letter-spacing:0.3em; text-transform:uppercase; text-decoration:none; color:{{ $filled ? '#2C3531' : '#D9B08D' }};">{{ $label }}</a>
        </td>
    </tr>
</table>
