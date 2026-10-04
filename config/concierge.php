<?php

/*
|--------------------------------------------------------------------------
| Private Concierge — acquisition requests
|--------------------------------------------------------------------------
|
| Orders are submitted as requests, reviewed by the atelier and settled
| offline. The settlement details below are sent to clients in approval
| emails and on their order page, so they MUST be set to the maison's real
| details in .env before going live — the defaults are placeholders.
|
*/

return [

    'notification_email' => env('CONCIERGE_NOTIFICATION_EMAIL', 'admin@elite.com'),

    /*
    | Public contact details (Contact page, emails). Contact-form inquiries are
    | delivered to `notification_email` above, alongside acquisition alerts.
    */
    'contact' => [
        'email' => env('CONCIERGE_PUBLIC_EMAIL', 'concierge@elite.watch'),
        'phone' => env('CONCIERGE_PUBLIC_PHONE', '+383 44 100 200'),
        'street' => env('CONCIERGE_STREET', 'Rr. Nëna Terezë'),
        'district' => env('CONCIERGE_DISTRICT', 'Qendra e Prishtinës'),
        'city' => env('CONCIERGE_CITY', '10000 Prishtina, Kosovo'),
        'latitude' => 42.6629,
        'longitude' => 21.1655,
        'hours' => [
            ['days' => 'Monday — Friday', 'time' => '10:00 — 19:00'],
            ['days' => 'Saturday', 'time' => '11:00 — 17:00'],
            ['days' => 'Sunday', 'time' => null], // by appointment only
        ],
        'timezone_note' => 'Central European Time (CEST / CET)',
    ],

    // Promised response time, used for client copy and the admin SLA badges.
    'sla_hours' => (int) env('CONCIERGE_SLA_HOURS', 24),

    // An admin's "being reviewed" marker is considered stale after this many minutes.
    'review_lock_minutes' => 30,

    'methods' => [
        'wire' => [
            'label' => 'Wire Transfer',
            'short' => 'Bank transfer',
            'icon' => 'heroicon-o-building-library',
            'description' => 'We will send you our IBAN details once your request is approved. Settlement within 7 days.',
        ],
        'boutique' => [
            'label' => 'At the Boutique',
            'short' => 'In-boutique',
            'icon' => 'heroicon-o-building-storefront',
            'description' => 'Visit us in Prishtina to inspect the piece and complete your acquisition in person.',
        ],
        'financing' => [
            'label' => 'Financing · 6, 12, or 24 months',
            'short' => 'Financing',
            'icon' => 'heroicon-o-calendar-days',
            'description' => 'Subject to approval. Our concierge will discuss terms with you directly.',
        ],
        'crypto' => [
            'label' => 'Digital Assets',
            'short' => 'Cryptocurrency',
            'icon' => 'heroicon-o-sparkles',
            'description' => 'We accept BTC, ETH, and USDT. Rate locked at the time of approval.',
        ],
    ],

    'wire' => [
        'beneficiary' => env('CONCIERGE_WIRE_BENEFICIARY', 'ÉLITE Maison Horlogère'),
        'bank' => env('CONCIERGE_WIRE_BANK', 'SET CONCIERGE_WIRE_BANK'),
        'iban' => env('CONCIERGE_WIRE_IBAN', 'SET CONCIERGE_WIRE_IBAN'),
        'bic' => env('CONCIERGE_WIRE_BIC', 'SET CONCIERGE_WIRE_BIC'),
        'settlement_days' => 7,
    ],

    'boutique' => [
        'address' => env('CONCIERGE_BOUTIQUE_ADDRESS', 'Prishtina, Kosovo'),
        'hours' => env('CONCIERGE_BOUTIQUE_HOURS', 'Tuesday – Saturday · 10:00 – 19:00'),
        // Where clients book a viewing; defaults to the site's contact page.
        'appointment_url' => env('CONCIERGE_APPOINTMENT_URL'),
    ],

    'financing' => [
        'terms' => [6, 12, 24],
    ],

    'crypto' => [
        'wallets' => [
            'BTC' => env('CONCIERGE_WALLET_BTC', 'SET CONCIERGE_WALLET_BTC'),
            'ETH' => env('CONCIERGE_WALLET_ETH', 'SET CONCIERGE_WALLET_ETH'),
            'USDT (ERC-20)' => env('CONCIERGE_WALLET_USDT', 'SET CONCIERGE_WALLET_USDT'),
        ],
    ],

];
