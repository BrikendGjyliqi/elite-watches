<?php

namespace App\Filament\Resources\WatchResource\Pages;

use App\Filament\Concerns\HasHeroHeader;
use App\Filament\Resources\WatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWatches extends ListRecords
{
    use HasHeroHeader;

    protected static string $resource = WatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
