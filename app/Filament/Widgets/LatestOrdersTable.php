<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Widgets\Widget;

class LatestOrdersTable extends Widget
{
    protected static ?int $sort = 7;

    protected static string $view = 'filament.widgets.latest-orders';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 2, 'xl' => 15];

    protected function getViewData(): array
    {
        return [
            'orders' => Order::with('user:id,name')->latest()->limit(8)->get(),
            'indexUrl' => OrderResource::getUrl('index'),
            'urlFor' => fn (Order $order): string => OrderResource::getUrl('view', ['record' => $order]),
        ];
    }
}
