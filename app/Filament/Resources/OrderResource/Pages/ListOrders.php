<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Concerns\HasHeroHeader;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    use HasHeroHeader;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
