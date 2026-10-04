<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Watch;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('shop.index'), 'priority' => '0.9'],
            ['loc' => route('brands.index'), 'priority' => '0.7'],
            ['loc' => route('about'), 'priority' => '0.5'],
            ['loc' => route('contact.index'), 'priority' => '0.5'],
        ]);

        Watch::query()->select(['slug', 'updated_at'])->each(function (Watch $watch) use ($urls) {
            $urls->push([
                'loc' => route('shop.show', $watch->slug),
                'lastmod' => $watch->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]);
        });

        Brand::query()->select(['slug', 'updated_at'])->each(function (Brand $brand) use ($urls) {
            $urls->push([
                'loc' => route('brands.show', $brand->slug),
                'lastmod' => $brand->updated_at?->toAtomString(),
                'priority' => '0.6',
            ]);
        });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
