<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Renders monogram avatars locally (teal disc, serif initials) instead of
 * calling out to ui-avatars.com.
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        return static::forName(Filament::getNameForDefaultAvatar($record));
    }

    public static function initials(?string $name): string
    {
        $initials = str($name ?? '')
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
            ->join('');

        return $initials !== '' ? $initials : 'É';
    }

    public static function forName(?string $name): string
    {
        $initials = e(static::initials($name));

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                <rect width="64" height="64" fill="#116466"/>
                <text x="50%" y="50%" dy=".36em" text-anchor="middle" fill="#ffffff"
                      font-family="'Playfair Display', Georgia, 'Times New Roman', serif" font-size="24" letter-spacing="1">{$initials}</text>
            </svg>
            SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
