@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'ogDescription' => null,
])

@php
    $siteName = 'ÉLITE';
    // A title that already names the maison is used as-is; otherwise the site name is appended.
    $fullTitle = $title
        ? (str_contains($title, $siteName) ? $title : "{$title} · {$siteName}")
        : "{$siteName} — Maison Horlogère";
    $metaDescription = $description ?: 'ÉLITE is a curated maison of the world\'s finest timepieces — Rolex, Patek Philippe, Audemars Piguet and more, for those who measure life in moments.';
    $ogImage = $image ?: asset('images/logo/elite-primary.svg');
    $socialDescription = $ogDescription ?: $metaDescription;
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ Str::limit($metaDescription, 160, '') }}">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ Str::limit($socialDescription, 200, '') }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ Str::limit($socialDescription, 200, '') }}">
<meta name="twitter:image" content="{{ $ogImage }}">
