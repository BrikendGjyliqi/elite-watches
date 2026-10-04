<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\WatchResource;
use App\Models\Order;
use App\Models\Watch;
use App\Support\WatchImageUrl;
use Filament\Widgets\Widget;

class TopSellingWatchesWidget extends Widget
{
    protected static ?int $sort = 8;

    protected static string $view = 'filament.widgets.top-selling-watches';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 2, 'xl' => 15];

    protected function getViewData(): array
    {
        $soldThisMonth = fn ($query) => $query->whereHas('order', fn ($order) => $order
            ->whereIn('status', Order::COMMITTED_STATUSES)
            ->where('created_at', '>=', now()->startOfMonth()));

        $watches = Watch::query()
            ->with(['brand:id,name', 'images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('sort_order')])
            ->whereHas('orderItems', $soldThisMonth)
            ->withSum(['orderItems as units_sold' => $soldThisMonth], 'quantity')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        return [
            'watches' => $watches,
            'imageFor' => fn (Watch $watch): ?string => WatchImageUrl::primary($watch),
            'urlFor' => fn (Watch $watch): string => WatchResource::getUrl('edit', ['record' => $watch]),
        ];
    }
}
