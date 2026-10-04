<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;

class CustomersKpiWidget extends KpiWidget
{
    protected static ?int $sort = 4;

    protected function getKpi(): array
    {
        $activeBetween = fn ($from, $to): int => User::where('role', 'customer')
            ->whereHas('orders', fn ($query) => $query->whereBetween('created_at', [$from, $to]))
            ->count();

        $current = $activeBetween(now()->subDays(30), now());
        $previous = $activeBetween(now()->subDays(60), now()->subDays(30));

        $newThisWeek = User::where('role', 'customer')->where('created_at', '>=', now()->subDays(7))->count();

        return [
            'label' => 'Active customers',
            'value' => $current,
            'money' => false,
            'sub' => $newThisWeek . ' new this week',
            'trend' => static::trend($current, $previous),
            'icon' => 'heroicon-o-user-group',
        ];
    }
}
