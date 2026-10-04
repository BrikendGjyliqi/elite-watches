<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = [
        'requested', 'under_review', 'approved', 'declined', 'awaiting_payment',
        'paid', 'shipped', 'delivered', 'cancelled',
    ];

    public const STATUS_LABELS = [
        'requested' => 'Requested',
        'under_review' => 'Under review',
        'approved' => 'Approved',
        'declined' => 'Declined',
        'awaiting_payment' => 'Awaiting payment',
        'paid' => 'Paid',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    /** Statuses whose totals count as realised revenue. */
    public const REVENUE_STATUSES = ['paid', 'shipped', 'delivered'];

    /** Approved onwards: the piece is committed to the client. */
    public const COMMITTED_STATUSES = ['approved', 'awaiting_payment', 'paid', 'shipped', 'delivered'];

    /** Requests the atelier still has to decide on. */
    public const OPEN_STATUSES = ['requested', 'under_review'];

    public const METHODS = ['wire', 'boutique', 'financing', 'crypto'];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'preferred_method',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'original_total',
        'stripe_payment_id',
        'shipping_address_id',
        'notes',
        'customer_note',
        'admin_response',
        'approved_at',
        'declined_at',
        'declined_reason',
        'reviewed_by',
        'reviewing_by',
        'reviewing_started_at',
    ];

    protected $attributes = [
        'status' => 'requested',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'original_total' => 'decimal:2',
            'approved_at' => 'datetime',
            'declined_at' => 'datetime',
            'reviewing_started_at' => 'datetime',
        ];
    }

    public function scopeRevenue($query)
    {
        return $query->whereIn('status', self::REVENUE_STATUSES);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviewingAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewing_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class)->oldest();
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function methodLabel(): ?string
    {
        return $this->preferred_method ? config("concierge.methods.{$this->preferred_method}.label") : null;
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    /** Settlement instructions are only shared once the atelier has said yes. */
    public function showsSettlementInstructions(): bool
    {
        return in_array($this->status, ['approved', 'awaiting_payment'], true) && $this->preferred_method !== null;
    }

    /** Another admin is actively reviewing this request (marker younger than the lock window). */
    public function isBeingReviewedByAnotherAdmin(?int $adminId): bool
    {
        return $this->reviewing_by !== null
            && $this->reviewing_by !== $adminId
            && $this->reviewing_started_at?->gt(now()->subMinutes(config('concierge.review_lock_minutes')));
    }

    /** Sequential, year-scoped request numbers: ELT-2026-00042. */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ELT-'.now()->format('Y').'-';

        $last = static::where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        do {
            $number = $prefix.str_pad((string) $sequence++, 5, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total', 'notes', 'preferred_method', 'declined_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
