<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Watch;
use App\Support\WatchImageUrl;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        // One query for every house's pieces (curated order), so cards need no extra lookups.
        $brands = Brand::query()
            ->withCount('watches')
            ->with(['watches' => fn ($q) => $q
                ->select(['id', 'brand_id', 'name', 'slug', 'price', 'discount_price', 'is_featured', 'is_bestseller'])
                ->orderByDesc('is_featured')->orderByDesc('is_bestseller')->orderByDesc('price')
                ->with(['images' => fn ($i) => $i->orderByDesc('is_primary')->orderBy('sort_order')])])
            ->showcaseOrder()
            ->get()
            ->each(function (Brand $brand) {
                $representative = $brand->watches->first(fn (Watch $w) => $w->images->isNotEmpty());
                $prices = $brand->watches->map(fn (Watch $w) => (float) ($w->discount_price ?? $w->price));

                $brand->setAttribute('card_image', WatchImageUrl::primary($representative));
                $brand->setAttribute('price_min', $prices->min());
                $brand->setAttribute('price_max', $prices->max());
            });

        // Maison of the month: a featured house, the one with the most pieces in the vault.
        $featured = $brands->sortBy([
            fn (Brand $a, Brand $b) => $b->is_featured <=> $a->is_featured,
            fn (Brand $a, Brand $b) => $b->watches_count <=> $a->watches_count,
            fn (Brand $a, Brand $b) => $a->name <=> $b->name,
        ])->first();

        $oldest = $brands->min('founded_year');

        return view('pages.brands.index', [
            'brands' => $brands,
            'featured' => $featured,
            'totalPieces' => (int) $brands->sum('watches_count'),
            'yearsOfHorology' => $oldest ? now()->year - $oldest : null,
        ]);
    }

    public function show(Request $request, Brand $brand)
    {
        $brand->loadCount('watches');

        $filters = $request->validate([
            'category' => ['nullable', 'integer'],
            'sort' => ['nullable', 'in:curated,price_asc,price_desc'],
        ]);

        $collection = $brand->watches()
            ->with(['brand', 'category', 'images'])
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category_id', $category));

        match ($filters['sort'] ?? 'curated') {
            'price_asc' => $collection->orderBy('price'),
            'price_desc' => $collection->orderByDesc('price'),
            default => $collection->orderByDesc('is_featured')->orderByDesc('is_bestseller')->orderByDesc('price'),
        };

        $representative = $brand->representativeWatch();

        return view('pages.brands.show', [
            'brand' => $brand,
            'maison' => $brand->maison(),
            'story' => $brand->storyParagraphs(),
            'signaturePieces' => $brand->signaturePieces(),
            'watches' => $collection->paginate(12)->withQueryString()->fragment('collection'),
            'categories' => Category::whereIn('id', $brand->watches()->select('category_id'))->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
            'related' => $brand->relatedBrands(5)->each(function (Brand $related) {
                $related->setAttribute('card_image', WatchImageUrl::primary($related->representativeWatch()));
            }),
            'priceRange' => $brand->priceRange(),
            'heroWatchImage' => WatchImageUrl::primary($representative),
        ]);
    }
}
