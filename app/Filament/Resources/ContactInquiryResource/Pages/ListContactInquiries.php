<?php

namespace App\Filament\Resources\ContactInquiryResource\Pages;

use App\Filament\Concerns\HasHeroHeader;
use App\Filament\Resources\ContactInquiryResource;
use Filament\Resources\Pages\ListRecords;

class ListContactInquiries extends ListRecords
{
    use HasHeroHeader;

    protected static string $resource = ContactInquiryResource::class;
}
