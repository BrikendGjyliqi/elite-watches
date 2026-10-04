<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Watch;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The storefront search drawer. Open/close, keyboard navigation and recent searches
 * are client-side (Alpine + localStorage); only querying happens here.
 */
class GlobalSearch extends Component
{
    public const MIN_LENGTH = 2;

    public const LIMIT = 8;

    public string $query = '';

    public function updatedQuery(): void
    {
        $this->query = mb_substr($this->query, 0, 80);

        unset($this->results, $this->totalResults, $this->brandMatches, $this->categoryMatches);
    }

    public function hasSearchableQuery(): bool
    {
        return mb_strlen(trim($this->query)) >= self::MIN_LENGTH && Watch::searchTerms($this->query) !== [];
    }

    /** Every word must match; if nothing does, fall back to pieces matching any word. */
    protected function matchAll(): bool
    {
        return Watch::query()->search($this->query)->exists();
    }

    /** @return Collection<int, Watch> */
    #[Computed]
    public function results(): Collection
    {
        if (! $this->hasSearchableQuery()) {
            return collect();
        }

        return Watch::query()
            ->search($this->query, $this->matchAll())
            ->with(['brand:id,name,slug', 'category:id,name', 'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_bestseller')
            ->orderBy('name')
            ->limit(self::LIMIT)
            ->get();
    }

    #[Computed]
    public function totalResults(): int
    {
        return $this->hasSearchableQuery()
            ? Watch::query()->search($this->query, $this->matchAll())->count()
            : 0;
    }

    /** @return Collection<int, Brand> */
    #[Computed]
    public function brandMatches(): Collection
    {
        return $this->namesLike(Brand::query());
    }

    /** @return Collection<int, Category> */
    #[Computed]
    public function categoryMatches(): Collection
    {
        return $this->namesLike(Category::query());
    }

    protected function namesLike($query): Collection
    {
        if (! $this->hasSearchableQuery()) {
            return collect();
        }

        $terms = Watch::searchTerms($this->query);

        return $query
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('name', 'like', '%'.addcslashes($term, '%_\\').'%');
                }
            })
            ->orderBy('name')
            ->limit(4)
            ->get(['id', 'name', 'slug']);
    }

    /**
     * The fixed suggestion pills: plain queries fill the input, the rest are curated shop views.
     *
     * @return array<int, array{label: string, query?: string, url?: string}>
     */
    public function suggestions(): array
    {
        $diveCategories = Category::whereIn('slug', ['diver', 'dive'])->pluck('id')->all();

        return [
            ['label' => 'Rolex Daytona', 'query' => 'Rolex Daytona'],
            ['label' => 'Patek Philippe Nautilus', 'query' => 'Patek Philippe Nautilus'],
            ['label' => 'Pilot chronograph', 'query' => 'Pilot chronograph'],
            ['label' => 'Dive watches under €10,000', 'url' => route('shop.index', ['category' => $diveCategories, 'max_price' => 10000])],
            ['label' => 'New arrivals', 'url' => route('shop.index', ['sort' => 'newest'])],
            ['label' => 'Audemars Piguet', 'query' => 'Audemars Piguet'],
        ];
    }

    public function render()
    {
        return view('livewire.global-search', [
            'suggestionPills' => $this->suggestions(),
        ]);
    }
}
