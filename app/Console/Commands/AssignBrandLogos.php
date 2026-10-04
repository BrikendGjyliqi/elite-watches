<?php

namespace App\Console\Commands;

use App\Models\Brand;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Points each brand's logo_path at its SVG in public/images/brands/.
 */
class AssignBrandLogos extends Command
{
    protected $signature = 'brands:assign-logos';

    protected $description = 'Assign the logo SVGs in public/images/brands/ to brands, matched by slug';

    /** Normalised slug => file in public/images/brands/. Aliases cover slug variations. */
    protected const LOGOS = [
        'rolex' => 'brand-rolex.svg',
        'omega' => 'brand-omega.svg',
        'patek-philippe' => 'brand-patek-philippe.svg',
        'audemars-piguet' => 'brand-audemars-piguet.svg',
        'cartier' => 'brand-cartier.svg',
        'tag-heuer' => 'brand-tag-heuer.svg',
        'breitling' => 'brand-breitling.svg',
        'iwc-schaffhausen' => 'brand-iwc.svg',
        'iwc' => 'brand-iwc.svg',
        'hublot' => 'brand-hublot.svg',
        'panerai' => 'brand-panerai.svg',
    ];

    public function handle(): int
    {
        $brands = Brand::orderBy('name')->get();
        $assigned = [];
        $unmatched = [];

        foreach ($brands as $brand) {
            // "tag_heuer", "TAG Heuer" and "tag-heuer" all normalise to "tag-heuer".
            $key = Str::slug(str_replace('_', '-', (string) $brand->slug));
            $file = self::LOGOS[$key] ?? null;

            if (! $file) {
                $unmatched[] = "{$brand->name} (slug: {$brand->slug}) — no logo in the mapping";
                continue;
            }

            if (! is_file(public_path("images/brands/{$file}"))) {
                $this->warn("{$brand->name}: public/images/brands/{$file} is missing, skipped.");
                $unmatched[] = "{$brand->name} (slug: {$brand->slug}) — file {$file} not found";
                continue;
            }

            $brand->logo_path = "images/brands/{$file}";
            $brand->save();
            $assigned[] = [$brand->name, $brand->slug, $brand->logo_path];
        }

        if ($assigned) {
            $this->table(['Brand', 'Slug', 'logo_path'], $assigned);
        }

        $this->info(sprintf('Assigned %d of %d brand logos', count($assigned), $brands->count()));

        foreach ($unmatched as $line) {
            $this->line("  Not matched: {$line}");
        }

        return self::SUCCESS;
    }
}
