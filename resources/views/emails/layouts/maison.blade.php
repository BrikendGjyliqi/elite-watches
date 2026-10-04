{{--
    ÉLITE email shell. Table layout + inline styles for email-client compatibility;
    the wordmark is text (SVG logos are stripped by Gmail and Outlook).
--}}
@php
    $gold = '#D9B08D';
    $mint = '#D1E8E2';
    $serif = "'Playfair Display', Georgia, 'Times New Roman', serif";
    $italic = "'Cormorant Garamond', Georgia, 'Times New Roman', serif";
    $sans = "'Inter', 'Helvetica Neue', Arial, sans-serif";
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="supported-color-schemes" content="dark">
    <title>@yield('title', 'ÉLITE')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Cormorant+Garamond:ital,wght@1,400;1,500&family=Inter:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0; padding:0; background:#1a2124; -webkit-font-smoothing:antialiased;">
    <span style="display:none; max-height:0; overflow:hidden; opacity:0;">@yield('preheader')</span>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#1a2124;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; background:#232c2e; border:1px solid rgba(217,176,141,0.2);">

                    {{-- Masthead --}}
                    <tr>
                        <td align="center" style="padding:44px 40px 0;">
                            <div style="font-family:{{ $serif }}; font-size:26px; letter-spacing:0.45em; color:{{ $gold }}; padding-left:0.45em;">ÉLITE</div>
                            <div style="font-family:{{ $italic }}; font-style:italic; font-size:11px; letter-spacing:0.35em; color:{{ $gold }}; opacity:0.7; margin-top:6px; text-transform:uppercase;">Maison Horlogère</div>
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:26px auto 0;">
                                <tr>
                                    {{-- Table cells ignore height:1px in most clients; a 1px div inside them doesn't. --}}
                                    <td valign="middle" style="width:64px;"><div style="height:1px; line-height:1px; font-size:1px; background:rgba(217,176,141,0.6);">&nbsp;</div></td>
                                    <td valign="middle" style="padding:0 10px; color:{{ $gold }}; font-size:8px; line-height:1;">&#9670;</td>
                                    <td valign="middle" style="width:64px;"><div style="height:1px; line-height:1px; font-size:1px; background:rgba(217,176,141,0.6);">&nbsp;</div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Heading --}}
                    <tr>
                        <td align="center" style="padding:28px 40px 0;">
                            @hasSection('eyebrow')
                                <div style="font-family:{{ $italic }}; font-style:italic; font-size:12px; letter-spacing:0.3em; text-transform:uppercase; color:{{ $gold }};">@yield('eyebrow')</div>
                            @endif
                            <h1 style="margin:12px 0 0; font-family:{{ $serif }}; font-weight:400; font-size:30px; line-height:1.25; color:#ffffff;">@yield('heading')</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px 44px 8px; font-family:{{ $sans }}; font-size:15px; line-height:1.7; color:{{ $mint }};">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Sign-off --}}
                    <tr>
                        <td style="padding:16px 44px 44px;">
                            <div style="height:1px; background:rgba(217,176,141,0.2); font-size:0; line-height:0; margin-bottom:24px;">&nbsp;</div>
                            <div style="font-family:{{ $italic }}; font-style:italic; font-size:18px; color:{{ $gold }};">— The ÉLITE Atelier</div>
                        </td>
                    </tr>
                </table>

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px;">
                    <tr>
                        <td align="center" style="padding:24px 16px; font-family:{{ $sans }}; font-size:11px; letter-spacing:0.22em; color:rgba(209,232,226,0.4); text-transform:uppercase;">
                            ÉLITE · Maison Horlogère · Prishtina
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
