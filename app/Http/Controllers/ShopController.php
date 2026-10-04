<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Watch;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Watch::query()
            ->with(['brand', 'category', 'images'])
            ->withCount('reviews');

        // Free-text search (/search?q=… and the search drawer's "View all" link): every word
        // must match; if nothing does, widen to pieces matching any word.
        $search = trim((string) $request->input('q'));
        if (Watch::searchTerms($search) !== []) {
            $matchAll = Watch::query()->search($search)->exists();
            $query->search($search, $matchAll);
        }

        // ?brand= accepts ids or slugs (/shop?brand=rolex); normalise to ids for the filter UI.
        if ($request->filled('brand')) {
            $requested = (array) $request->input('brand');
            $slugs = array_filter($requested, fn ($value) => ! is_numeric($value));
            $ids = array_merge(
                array_values(array_filter($requested, 'is_numeric')),
                $slugs ? Brand::whereIn('slug', $slugs)->pluck('id')->all() : [],
            );
            $request->merge(['brand' => array_values(array_unique(array_map('intval', $ids)))]);
        }

        $query->when($request->filled('brand'), function ($q) use ($request) {
            $q->whereIn('brand_id', (array) $request->input('brand'));
        });

        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->whereIn('category_id', (array) $request->input('category'));
        });

        $query->when($request->filled('min_price'), function ($q) use ($request) {
            $q->where('price', '>=', (float) $request->input('min_price'));
        });

        $query->when($request->filled('max_price'), function ($q) use ($request) {
            $q->where('price', '<=', (float) $request->input('max_price'));
        });

        $query->when($request->boolean('in_stock'), function ($q) {
            $q->where('stock', '>', 0);
        });

        match ($request->input('sort', 'newest')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderBy('reviews_count', 'desc'),
            default => $query->latest(),
        };

        $watches = $query->paginate(12)->withQueryString();

        return view('pages.shop', [
            'watches' => $watches,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->only(['q', 'brand', 'category', 'min_price', 'max_price', 'in_stock', 'sort']),
        ]);
    }

    public function show(Watch $watch)
    {
        $watch->load([
            'brand',
            'category',
            'spec',
            'images',
            'reviews' => fn ($q) => $q->where('is_approved', true)->with('user')->latest(),
        ]);

        $relatedWatches = Watch::with(['brand', 'images'])
            ->where('id', '!=', $watch->id)
            ->where(function ($q) use ($watch) {
                $q->where('brand_id', $watch->brand_id)
                    ->orWhere('category_id', $watch->category_id);
            })
            ->take(4)
            ->get();

        return view('pages.product', [
            'watch' => $watch,
            'relatedWatches' => $relatedWatches,
        ]);
    }
}
