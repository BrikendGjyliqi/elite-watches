<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A message sent through the Contact page.
 */
class ContactInquiry extends Model
{
    public const SUBJECTS = [
        'acquisition' => 'An acquisition inquiry',
        'unlisted' => 'A piece not listed',
        'order' => 'An existing order',
        'appointment' => 'A private appointment',
        'press' => 'Press & partnerships',
        'other' => 'Something else',
    ];

    public const CHANNELS = [
        'email' => 'Email',
        'phone' => 'Phone',
        'whatsapp' => 'WhatsApp',
    ];

    public const STATUSES = [
        'new' => 'New',
        'replied' => 'Replied',
        'archived' => 'Archived',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'preferred_channel',
        'message',
        'status',
        'reply',
        'replied_at',
        'replied_by',
    ];

    protected $attributes = [
        'status' => 'new',
        'preferred_channel' => 'email',
    ];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /** Client-facing reference, e.g. INQ-2026-00042. */
    public function reference(): string
    {
        return 'INQ-'.($this->created_at ?? now())->format('Y').'-'.str_pad((string) $this->getKey(), 5, '0', STR_PAD_LEFT);
    }

    public function subjectLabel(): string
    {
        return self::SUBJECTS[$this->subject] ?? ucfirst((string) $this->subject);
    }

    public function channelLabel(): string
    {
        return self::CHANNELS[$this->preferred_channel] ?? ucfirst((string) $this->preferred_channel);
    }
}
