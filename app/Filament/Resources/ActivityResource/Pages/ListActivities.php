<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Concerns\HasHeroHeader;
use App\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    use HasHeroHeader;

    protected static string $resource = ActivityResource::class;
}
