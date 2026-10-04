<?php

namespace App\Filament\Widgets;

use App\Models\Order;

class OrdersKpiWidget extends KpiWidget
{
    protected static ?int $sort = 2;

    protected function getKpi(): array
    {
        [$thisMonth, $lastMonth] = static::monthToDateWindows();

        $current = Order::whereBetween('created_at', $thisMonth)->count();
        $previous = Order::whereBetween('created_at', $lastMonth)->count();

        $today = Order::where('created_at', '>=', today())->count();

        return [
            'label' => 'Orders · This month',
            'value' => $current,
            'money' => false,
            'sub' => $today . ' new today',
            'trend' => static::trend($current, $previous),
            'icon' => 'heroicon-o-shopping-bag',
        ];
    }
}
