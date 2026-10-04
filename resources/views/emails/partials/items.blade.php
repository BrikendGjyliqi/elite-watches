{{-- Line items + total for an acquisition request. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0; border-top:1px solid rgba(217,176,141,0.2);">
    @foreach ($order->items as $item)
        <tr>
            <td style="padding:14px 0; border-bottom:1px solid rgba(217,176,141,0.1);">
                <div style="font-family:'Cormorant Garamond',Georgia,serif; font-style:italic; font-size:12px; letter-spacing:0.25em; text-transform:uppercase; color:#D9B08D;">{{ $item->watch?->brand?->name }}</div>
                <div style="font-family:'Playfair Display',Georgia,serif; font-size:16px; color:#ffffff;">{{ $item->watch?->name ?? 'A piece no longer listed' }}</div>
                <div style="font-family:'Inter',Arial,sans-serif; font-size:12px; color:rgba(209,232,226,0.6);">Qty {{ $item->quantity }}@if ($item->watch?->reference_number) · Ref. {{ $item->watch->reference_number }}@endif</div>
            </td>
            <td align="right" valign="middle" style="padding:14px 0; border-bottom:1px solid rgba(217,176,141,0.1); font-family:'Playfair Display',Georgia,serif; font-size:16px; color:#D9B08D; white-space:nowrap;">
                €{{ number_format((float) $item->subtotal, 2, ',', '.') }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td style="padding:16px 0 0; font-family:'Inter',Arial,sans-serif; font-size:12px; letter-spacing:0.2em; text-transform:uppercase; color:#ffffff;">
            Total
            @if ($order->original_total && (float) $order->original_total !== (float) $order->total)
                <span style="display:block; font-size:11px; letter-spacing:0.05em; text-transform:none; color:rgba(209,232,226,0.55); margin-top:4px;">Adjusted by the atelier from €{{ number_format((float) $order->original_total, 2, ',', '.') }}</span>
            @endif
        </td>
        <td align="right" style="padding:16px 0 0; font-family:'Playfair Display',Georgia,serif; font-size:24px; color:#D9B08D; white-space:nowrap;">
            €{{ number_format((float) $order->total, 2, ',', '.') }}
        </td>
    </tr>
</table>
