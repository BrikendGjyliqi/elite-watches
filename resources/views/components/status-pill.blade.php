@props(['status'])

@php
    $styles = [
        'requested' => 'border-accent-gold/70 text-accent-gold',
        'under_review' => 'border-accent-gold text-accent-gold bg-accent-gold/10 acq-pill-pulse',
        'approved' => 'border-primary-teal bg-primary-teal text-white',
        'awaiting_payment' => 'border-accent-peach bg-accent-peach text-primary-dark',
        'declined' => 'border-[#E8A598]/60 text-[#E8A598]',
        'paid' => 'border-text-mint bg-text-mint text-primary-dark',
        'shipped' => 'border-accent-gold bg-accent-gold text-primary-dark',
        'delivered' => 'border-[#7fa89d] bg-[#5b8178] text-white',
        'cancelled' => 'border-text-mint/25 text-text-mint/50',
    ];

    $classes = $styles[$status] ?? $styles['requested'];
    $label = \App\Models\Order::STATUS_LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->class(["inline-flex items-center px-3 py-1 text-[11px] uppercase tracking-widest border whitespace-nowrap {$classes}"]) }}>
    {{ $label }}
</span>
