<?php

namespace App\Filament\Widgets;

use App\Models\Order;

class AovKpiWidget extends KpiWidget
{
    protected static ?int $sort = 3;

    protected function getKpi(): array
    {
        $allTime = (float) Order::revenue()->avg('total');

        $recent = (float) Order::revenue()->where('created_at', '>=', now()->subDays(30))->avg('total');
        $prior = (float) Order::revenue()->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->avg('total');

        return [
            'label' => 'Average order value',
            'value' => $allTime,
            'money' => true,
            'sub' => 'across all time',
            'trend' => static::trend($recent, $prior),
            'icon' => 'heroicon-o-scale',
        ];
    }
}
