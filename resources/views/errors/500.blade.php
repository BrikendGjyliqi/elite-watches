<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ÉLITE') }}</title>
        <meta name="theme-color" content="#2C3531">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300..700&family=Inter:wght@300..700&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet" />
        <style>
            body { margin: 0; background: #2C3531; color: #D1E8E2; font-family: 'Inter', sans-serif; }
            .wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; text-align: center; }
            h1 { font-family: 'Playfair Display', serif; color: #D9B08D; font-size: 6rem; margin: 0 0 1.5rem; }
            .eyebrow { font-family: 'Cormorant Garamond', serif; font-style: italic; color: #FFCB9A; letter-spacing: 0.3em; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 1rem; }
            h2 { font-family: 'Playfair Display', serif; color: #fff; font-size: 1.75rem; margin: 0 0 1rem; }
            p { color: rgba(209, 232, 226, 0.7); max-width: 32rem; margin: 0 auto 2.5rem; line-height: 1.6; }
            a { display: inline-flex; align-items: center; justify-content: center; padding: 0.9rem 2rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.2em; background: #116466; color: #fff; text-decoration: none; transition: opacity 0.2s; }
            a:hover { opacity: 0.85; }
        </style>
    </head>
    <body>
        <div class="wrap">
            <div>
                <h1>500</h1>
                <p class="eyebrow">{{ __('A Rare Fault') }}</p>
                <h2>{{ __('Something went wrong on our end') }}</h2>
                <p>{{ __('Our atelier has been notified. Please try again in a moment.') }}</p>
                <a href="{{ url('/') }}">{{ __('Back to Home') }}</a>
            </div>
        </div>
    </body>
</html>
