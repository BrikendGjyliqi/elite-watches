<?php

namespace App\Filament\Widgets;

use App\Models\Order;

class RevenueKpiWidget extends KpiWidget
{
    protected static ?int $sort = 1;

    protected function getKpi(): array
    {
        // Month-to-date against the same span of last month, so the trend is like-for-like.
        [$thisMonth, $lastMonth] = static::monthToDateWindows();

        $current = (float) Order::revenue()->whereBetween('created_at', $thisMonth)->sum('total');
        $previous = (float) Order::revenue()->whereBetween('created_at', $lastMonth)->sum('total');

        return [
            'label' => 'Revenue · This month',
            'value' => $current,
            'money' => true,
            'sub' => 'vs. ' . static::euros($previous) . ' this time last month',
            'trend' => static::trend($current, $previous),
            'icon' => 'heroicon-o-banknotes',
        ];
    }
}
