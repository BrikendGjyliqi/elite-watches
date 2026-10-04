<?php

namespace App\Support;

use App\Models\Order;

/**
 * What the client must do to settle an approved request, per preferred method.
 * Rendered by both the approval email and the client's order page.
 */
class SettlementInstructions
{
    /**
     * @return array{method: string, title: string, intro: string, details: array<string, string>, note: ?string, cta: ?array{label: string, url: string}}
     */
    public static function for(Order $order): array
    {
        $amount = '€'.number_format((float) $order->total, 2, ',', '.');

        return match ($order->preferred_method) {
            'wire' => [
                'method' => 'wire',
                'title' => 'Settlement by wire transfer',
                'intro' => 'Please transfer '.$amount.' within '.config('concierge.wire.settlement_days').' days using the details below. Your piece is reserved for you in the meantime.',
                'details' => [
                    'Beneficiary' => config('concierge.wire.beneficiary'),
                    'Bank' => config('concierge.wire.bank'),
                    'IBAN' => config('concierge.wire.iban'),
                    'BIC / SWIFT' => config('concierge.wire.bic'),
                    'Reference' => $order->order_number,
                    'Amount' => $amount,
                ],
                'note' => 'Always quote the reference so we can match your transfer.',
                'cta' => null,
            ],
            'boutique' => [
                'method' => 'boutique',
                'title' => 'Settlement at the boutique',
                'intro' => 'Your piece is reserved and waiting for you. Choose a time that suits you and we will prepare a private viewing.',
                'details' => [
                    'Boutique' => config('concierge.boutique.address'),
                    'Hours' => config('concierge.boutique.hours'),
                    'Reference' => $order->order_number,
                    'Amount' => $amount,
                ],
                'note' => 'Please bring a valid photo ID.',
                'cta' => ['label' => 'Book your appointment', 'url' => config('concierge.boutique.appointment_url') ?: route('contact.index')],
            ],
            'financing' => [
                'method' => 'financing',
                'title' => 'Settlement by financing',
                'intro' => 'Our concierge will contact you to agree terms. Plans are available over '.implode(', ', array_map(fn ($m) => $m.' months', config('concierge.financing.terms'))).', subject to approval.',
                'details' => [
                    'Financed amount' => $amount,
                    'Available terms' => implode(' · ', array_map(fn ($m) => $m.' months', config('concierge.financing.terms'))),
                    'Reference' => $order->order_number,
                ],
                'note' => 'No payment is due until your financing agreement is signed.',
                'cta' => null,
            ],
            'crypto' => [
                'method' => 'crypto',
                'title' => 'Settlement in digital assets',
                'intro' => 'Your rate is locked as of approval. Our concierge will confirm the exact amount in your chosen asset before you send anything.',
                'details' => [
                    ...config('concierge.crypto.wallets'),
                    'Reference' => $order->order_number,
                    'Amount (EUR)' => $amount,
                ],
                'note' => 'Wait for our confirmation of the exact amount before transferring.',
                'cta' => null,
            ],
            default => [
                'method' => 'other',
                'title' => 'Settlement',
                'intro' => 'Our concierge will contact you to arrange settlement.',
                'details' => ['Reference' => $order->order_number, 'Amount' => $amount],
                'note' => null,
                'cta' => null,
            ],
        };
    }
}
