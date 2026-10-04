<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Concerns\HasHeroHeader;
use App\Filament\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    use HasHeroHeader;

    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
