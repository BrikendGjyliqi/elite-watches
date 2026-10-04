<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number')->label('Order #'),
            ExportColumn::make('user.name')->label('Customer'),
            ExportColumn::make('user.email')->label('Email'),
            ExportColumn::make('status'),
            ExportColumn::make('subtotal'),
            ExportColumn::make('tax'),
            ExportColumn::make('shipping'),
            ExportColumn::make('total'),
            ExportColumn::make('preferred_method')->label('Preferred settlement'),
            ExportColumn::make('approved_at'),
            ExportColumn::make('declined_reason'),
            ExportColumn::make('created_at')->label('Placed at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
