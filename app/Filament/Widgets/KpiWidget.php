<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * Shared shell for the dashboard's headline figures. Subclasses only supply numbers.
 */
abstract class KpiWidget extends Widget
{
    protected static string $view = 'filament.widgets.kpi';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 1, 'xl' => 6];

    /**
     * @return array{label: string, value: float|int, money: bool, sub: string, trend: ?float, icon: string}
     */
    abstract protected function getKpi(): array;

    protected function getViewData(): array
    {
        return ['kpi' => $this->getKpi()];
    }

    /** Percentage change, or null when there is nothing to compare against. */
    protected static function trend(float $current, float $previous): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Month-to-date window and the matching span of last month (clamped to its last day).
     *
     * @return array{0: array{0: \Carbon\CarbonInterface, 1: \Carbon\CarbonInterface}, 1: array{0: \Carbon\CarbonInterface, 1: \Carbon\CarbonInterface}}
     */
    protected static function monthToDateWindows(): array
    {
        $now = now();
        $lastStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastEnd = $lastStart->copy()->addDays($now->day - 1)->setTimeFrom($now);

        return [
            [$now->copy()->startOfMonth(), $now],
            [$lastStart, $lastEnd->min($lastStart->copy()->endOfMonth())],
        ];
    }

    protected static function euros(float $amount): string
    {
        return '€' . number_format($amount, 2);
    }
}
