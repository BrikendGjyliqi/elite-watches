<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your acquisition has been approved · ÉLITE');
    }

    public function content(): Content
    {
        $this->order->loadMissing(['user', 'items.watch.brand', 'shippingAddress']);

        return new Content(view: 'emails.acquisitions.approved');
    }
}
