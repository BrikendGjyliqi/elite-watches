<?php

namespace App\Support;

use App\Models\Watch;
use App\Models\WatchImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Resolves a watch image path to a URL. Seeded images live under public/ ("/images/…")
 * or are remote; admin uploads live on the Filament upload disk.
 */
class WatchImageUrl
{
    public static function for(?WatchImage $image): ?string
    {
        if (! $image) {
            return null;
        }

        return match (true) {
            Str::startsWith($image->path, ['http://', 'https://']) => $image->path,
            Str::startsWith($image->path, '/') => asset($image->path),
            default => Storage::disk(config('filament.default_filesystem_disk', 'public'))->url($image->path),
        };
    }

    /** Primary image first, then the lowest sort order. Uses the loaded relation when present. */
    public static function primary(?Watch $watch): ?string
    {
        if (! $watch) {
            return null;
        }

        $images = $watch->relationLoaded('images') ? $watch->images : $watch->images()->get();

        return static::for(
            $images->sortBy([['is_primary', 'desc'], ['sort_order', 'asc']])->first()
        );
    }
}
