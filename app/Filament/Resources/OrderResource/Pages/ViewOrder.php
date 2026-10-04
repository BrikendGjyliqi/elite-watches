<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\AcquisitionResource;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openDossier')
                ->label('Open dossier')
                ->icon('heroicon-o-folder-open')
                ->color('gray')
                ->url(fn (): string => AcquisitionResource::reviewUrl($this->getRecord())),
            OrderResource::updateStatusHeaderAction(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('order_number')->label('Order #'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => Order::STATUS_LABELS[$state] ?? $state)
                            ->color(fn (string $state): string => AcquisitionResource::statusColor($state)),
                        TextEntry::make('created_at')->label('Placed at')->dateTime(),
                        TextEntry::make('subtotal')->money('EUR'),
                        TextEntry::make('tax')->money('EUR'),
                        TextEntry::make('shipping')->money('EUR'),
                        TextEntry::make('total')->money('EUR'),
                        TextEntry::make('preferred_method')
                            ->label('Preferred settlement')
                            ->formatStateUsing(fn (?string $state): string => $state ? config("concierge.methods.{$state}.label") : '—'),
                        TextEntry::make('reviewer.name')->label('Reviewed by')->placeholder('—'),
                        TextEntry::make('approved_at')->dateTime()->placeholder('—'),
                        TextEntry::make('declined_reason')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('customer_note')->label('Client note')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('notes')->label('Internal note')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make('Customer')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')->label('Name'),
                        TextEntry::make('user.email')->label('Email'),
                        TextEntry::make('user.phone')->label('Phone'),
                    ]),

                Section::make('Shipping address')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('shippingAddress.full_name')->label('Recipient'),
                        TextEntry::make('shippingAddress.phone')->label('Phone'),
                        TextEntry::make('shippingAddress.street')->label('Street'),
                        TextEntry::make('shippingAddress.city')->label('City'),
                        TextEntry::make('shippingAddress.state')->label('State'),
                        TextEntry::make('shippingAddress.postal_code')->label('Postal code'),
                        TextEntry::make('shippingAddress.country')->label('Country'),
                    ]),
            ]);
    }
}
