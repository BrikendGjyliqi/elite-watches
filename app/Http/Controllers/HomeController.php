<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Review;
use App\Models\Watch;

class HomeController extends Controller
{
    public function index()
    {
        $featuredWatches = Watch::with(['brand', 'category', 'images'])
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $newArrivals = Watch::with(['brand', 'category', 'images'])
            ->where('is_new', true)
            ->latest()
            ->take(8)
            ->get();

        $featuredBrands = Brand::where('is_featured', true)
            ->orderBy('name')
            ->get();

        $brands = Brand::orderBy('name')->get();

        $heroWatch = Watch::with(['brand', 'images'])
            ->where('name', 'like', '%Daytona%')
            ->first() ?? $featuredWatches->first();

        $heroWatches = collect([$heroWatch])
            ->merge($featuredWatches)
            ->merge($newArrivals)
            ->filter()
            ->unique('id')
            ->take(6)
            ->values();

        $testimonials = Review::with(['user', 'watch'])
            ->where('is_approved', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(3)
            ->get();

        $spotlightCategories = Category::whereIn('name', ['Diver', 'Dress', 'Chronograph', 'Pilot'])
            ->get()
            ->sortBy(fn ($category) => array_search($category->name, ['Diver', 'Dress', 'Chronograph', 'Pilot']))
            ->values();

        return view('pages.home', [
            'featuredWatches' => $featuredWatches,
            'newArrivals' => $newArrivals,
            'featuredBrands' => $featuredBrands,
            'brands' => $brands,
            'heroWatch' => $heroWatch,
            'heroWatches' => $heroWatches,
            'testimonials' => $testimonials,
            'spotlightCategories' => $spotlightCategories,
        ]);
    }
}
