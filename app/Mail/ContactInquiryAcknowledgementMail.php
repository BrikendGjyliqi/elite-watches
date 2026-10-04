<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactInquiryAcknowledgementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We've received your message · ÉLITE",
            // Replies from the client reach the atelier, not a no-reply sender.
            replyTo: [new Address(config('concierge.contact.email'), 'The ÉLITE Atelier')],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inquiries.acknowledgement');
    }
}
