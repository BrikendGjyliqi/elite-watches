<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Watch extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'brand_id',
        'category_id',
        'reference_number',
        'price',
        'discount_price',
        'stock',
        'description',
        'short_description',
        'is_featured',
        'is_new',
        'is_bestseller',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_bestseller' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function spec(): HasOne
    {
        return $this->hasOne(WatchSpec::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(WatchImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeLowStock($query, int $threshold = 3)
    {
        return $query->where('stock', '<', $threshold);
    }

    /**
     * Words of a search phrase, lower-cased, at least 2 characters, at most 6.
     *
     * @return array<int, string>
     */
    public static function searchTerms(?string $phrase): array
    {
        return collect(preg_split('/\s+/u', mb_strtolower(trim((string) $phrase))))
            ->map(fn (string $word): string => trim($word, " \t\n\r\0\x0B.,;:!?\"'()"))
            ->filter(fn (string $word): bool => mb_strlen($word) >= 2)
            ->unique()
            ->take(6)
            ->values()
            ->all();
    }

    /**
     * Each word must appear in the name, reference, brand or category ($matchAll),
     * or — for a looser fallback — any one word is enough.
     */
    public function scopeSearch(Builder $query, ?string $phrase, bool $matchAll = true): Builder
    {
        $terms = static::searchTerms($phrase);

        if ($terms === []) {
            return $query;
        }

        $matchesTerm = function (Builder $q, string $term): void {
            $like = '%'.addcslashes($term, '%_\\').'%';

            $q->where('name', 'like', $like)
                ->orWhere('reference_number', 'like', $like)
                ->orWhereHas('brand', fn (Builder $brand) => $brand->where('name', 'like', $like))
                ->orWhereHas('category', fn (Builder $category) => $category->where('name', 'like', $like));
        };

        return $query->where(function (Builder $outer) use ($terms, $matchesTerm, $matchAll): void {
            foreach ($terms as $term) {
                $matchAll
                    ? $outer->where(fn (Builder $q) => $matchesTerm($q, $term))
                    : $outer->orWhere(fn (Builder $q) => $matchesTerm($q, $term));
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
