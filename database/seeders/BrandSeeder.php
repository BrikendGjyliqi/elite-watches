<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Rolex', 'country' => 'Switzerland', 'founded_year' => 1905, 'is_featured' => true, 'description' => 'The benchmark of Swiss watchmaking, Rolex has defined precision and prestige for over a century with icons like the Submariner and Daytona.'],
            ['name' => 'Omega', 'country' => 'Switzerland', 'founded_year' => 1848, 'is_featured' => true, 'description' => 'Official timekeeper of the Olympic Games and the watch that went to the Moon, Omega blends adventurous heritage with master chronometer precision.'],
            ['name' => 'Patek Philippe', 'country' => 'Switzerland', 'founded_year' => 1839, 'is_featured' => true, 'description' => 'One of the last independent family-owned Genevan manufactures, Patek Philippe is revered for haute horlogerie and multi-generational heirlooms.'],
            ['name' => 'Audemars Piguet', 'country' => 'Switzerland', 'founded_year' => 1875, 'is_featured' => true, 'description' => 'Pioneers of the luxury sports watch with the iconic Royal Oak, Audemars Piguet remains fiercely independent and endlessly innovative.'],
            ['name' => 'Cartier', 'country' => 'France', 'founded_year' => 1847, 'is_featured' => false, 'description' => 'The jeweler of kings, Cartier brought art deco elegance to the wrist with timeless silhouettes like the Tank and Santos.'],
            ['name' => 'TAG Heuer', 'country' => 'Switzerland', 'founded_year' => 1860, 'is_featured' => false, 'description' => 'A racing and motorsport pedigree runs through TAG Heuer, makers of the legendary Carrera and pioneers of precision chronograph timing.'],
            ['name' => 'Breitling', 'country' => 'Switzerland', 'founded_year' => 1884, 'is_featured' => false, 'description' => 'The choice of pilots and aviators since the golden age of flight, Breitling is synonymous with the instrument chronograph.'],
            ['name' => 'IWC Schaffhausen', 'country' => 'Switzerland', 'founded_year' => 1868, 'is_featured' => false, 'description' => 'Engineering-driven Swiss watchmaking from the town of Schaffhausen, celebrated for the Portugieser and Pilot\'s Watch collections.'],
            ['name' => 'Hublot', 'country' => 'Switzerland', 'founded_year' => 1980, 'is_featured' => false, 'description' => 'The art of fusion — Hublot pairs unconventional materials like ceramic and carbon with bold, contemporary design.'],
            ['name' => 'Panerai', 'country' => 'Italy', 'founded_year' => 1860, 'is_featured' => false, 'description' => 'Born from Italian naval history, Panerai\'s oversized cushion cases and legible dials trace back to instruments built for combat divers.'],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                ...$brand,
                'slug' => Str::slug($brand['name']),
                'logo_path' => Str::slug($brand['name']).'.svg',
            ]);
        }
    }
}
