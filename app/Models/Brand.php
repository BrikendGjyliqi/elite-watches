<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'country',
        'founded_year',
        'description',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'founded_year' => 'integer',
        ];
    }

    public function watches(): HasMany
    {
        return $this->hasMany(Watch::class);
    }

    /** Pieces in the order the maison presents them: featured, then bestsellers, then by price. */
    public function curatedWatches(): HasMany
    {
        return $this->watches()
            ->orderByDesc('is_featured')
            ->orderByDesc('is_bestseller')
            ->orderByDesc('price');
    }

    /**
     * Editorial details from config/maisons.php merged over the defaults.
     *
     * @return array{tagline: string, framing: string, accent: string, hero_image: ?string, city?: string, founder?: string, signature?: array{year: string, name: string, note: string}}
     */
    public function maison(): array
    {
        return array_merge(config('maisons.defaults'), config("maisons.{$this->slug}", []));
    }

    /** The three pieces to know first. */
    public function signaturePieces(): Collection
    {
        return $this->curatedWatches()
            ->with(['images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'), 'category:id,name'])
            ->limit(3)
            ->get();
    }

    /**
     * Kindred houses: same country first, then the nearest founding era, then the rest.
     *
     * @return Collection<int, Brand>
     */
    public function relatedBrands(int $limit = 5): Collection
    {
        return static::query()
            ->whereKeyNot($this->getKey())
            ->withCount('watches')
            ->get()
            ->sortBy([
                fn (Brand $a, Brand $b) => ($b->country === $this->country) <=> ($a->country === $this->country),
                fn (Brand $a, Brand $b) => abs(($a->founded_year ?? 9999) - ($this->founded_year ?? 0)) <=> abs(($b->founded_year ?? 9999) - ($this->founded_year ?? 0)),
                fn (Brand $a, Brand $b) => $a->name <=> $b->name,
            ])
            ->take($limit)
            ->values();
    }

    /** The piece whose photograph represents the house (first curated piece with an image). */
    public function representativeWatch(): ?Watch
    {
        return $this->curatedWatches()
            ->whereHas('images')
            ->with(['images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])
            ->first();
    }

    /**
     * The story, as paragraphs: the stored description (split on blank lines), a line on the
     * house's origins, and two closing paragraphs on how the atelier approaches its pieces.
     *
     * @return array<int, string>
     */
    public function storyParagraphs(): array
    {
        $maison = $this->maison();

        $paragraphs = collect(preg_split('/\R{2,}/', trim((string) $this->description)))
            ->map(fn (string $p) => trim($p))
            ->filter();

        if (! empty($maison['founder']) && $this->founded_year) {
            $origin = "The house traces its beginnings to {$this->founded_year}, to {$maison['founder']}.";

            if (! empty($maison['signature'])) {
                $origin .= " In {$maison['signature']['year']}, {$maison['signature']['name']} gave it a signature that collectors still seek out today — ".lcfirst($maison['signature']['note']).'.';
            }

            $paragraphs->push($origin);
        }

        if ($paragraphs->count() < 4) {
            $paragraphs->push("What endures in a {$this->name} is not only its mechanism but its intent: a way of measuring time that has been refined, questioned and refined again across generations of watchmakers.");
            $paragraphs->push("Every {$this->name} that enters the ÉLITE atelier is examined by hand — its movement, case, dial and papers — before it is offered to its next keeper.");
        }

        return $paragraphs->values()->all();
    }

    /**
     * Public URL of the logo. Bundled logos live in public/ ("images/brands/…"); logos
     * uploaded through the admin live on the "public" storage disk ("brand-logos/…").
     */
    public function logoUrl(): ?string
    {
        $path = $this->logo_path;

        return match (true) {
            blank($path) => null,
            Str::startsWith($path, ['http://', 'https://']) => $path,
            Str::startsWith($path, ['/', 'images/']) => asset(ltrim($path, '/')),
            default => Storage::disk('public')->url($path),
        };
    }

    /** Lowest and highest asking price across the house's pieces (discounts considered). */
    public function priceRange(): ?array
    {
        $prices = $this->watches()->get(['price', 'discount_price'])
            ->map(fn (Watch $w) => (float) ($w->discount_price ?? $w->price));

        return $prices->isEmpty() ? null : ['min' => $prices->min(), 'max' => $prices->max()];
    }

    public function scopeShowcaseOrder(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('name');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
