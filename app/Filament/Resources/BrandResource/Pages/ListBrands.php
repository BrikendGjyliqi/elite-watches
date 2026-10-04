<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Concerns\HasHeroHeader;
use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    use HasHeroHeader;

    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
