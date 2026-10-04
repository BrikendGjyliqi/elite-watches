<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Diver', 'description' => 'Built for the depths — robust water resistance, unidirectional bezels, and legible dials.'],
            ['name' => 'Dress', 'description' => 'Slim, elegant timepieces designed for tailored suits and formal occasions.'],
            ['name' => 'Pilot', 'description' => 'Cockpit-inspired watches with oversized crowns and high-contrast dials for aviators.'],
            ['name' => 'Chronograph', 'description' => 'Precision stopwatch complications for timing, racing, and everyday versatility.'],
            ['name' => 'Sport', 'description' => 'Integrated-bracelet luxury sport watches built for both the boardroom and the weekend.'],
            ['name' => 'GMT', 'description' => 'Dual and multi-timezone watches for frequent travelers.'],
            ['name' => 'Dive', 'description' => 'Professional-grade dive instruments engineered for extreme underwater performance.'],
        ];

        foreach ($categories as $category) {
            Category::create([
                ...$category,
                'slug' => Str::slug($category['name']),
            ]);
        }
    }
}
