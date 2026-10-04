<?php

namespace App\Filament\Concerns;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Gives a resource list page its hero-band subheading: the live record count.
 * The band itself is styled in resources/css/filament/admin/theme.css.
 */
trait HasHeroHeader
{
    public function getSubheading(): string | Htmlable | null
    {
        $count = static::getResource()::getEloquentQuery()->count();

        $label = $count === 1
            ? static::getResource()::getModelLabel()
            : static::getResource()::getPluralModelLabel();

        return number_format($count) . ' ' . $label;
    }
}
