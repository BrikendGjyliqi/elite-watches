<?php

namespace App\Filament\Pages;

use App\Filament\Widgets;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public function getTitle(): string | Htmlable
    {
        return 'Dashboard';
    }

    /** The hero band carries the page context, so Filament's heading is suppressed. */
    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getColumns(): int | string | array
    {
        return ['default' => 1, 'md' => 2, 'xl' => 30];
    }

    public function getWidgets(): array
    {
        return [
            Widgets\RevenueKpiWidget::class,
            Widgets\OrdersKpiWidget::class,
            Widgets\AovKpiWidget::class,
            Widgets\CustomersKpiWidget::class,
            Widgets\AvgApprovalTimeKpiWidget::class,
            Widgets\PendingAcquisitionsWidget::class,
            Widgets\RevenueChartWidget::class,
            Widgets\OrderStatusBreakdownWidget::class,
            Widgets\LatestOrdersTable::class,
            Widgets\TopSellingWatchesWidget::class,
            Widgets\StockAlertsTable::class,
            Widgets\PendingReviewsWidget::class,
            Widgets\RecentCustomersWidget::class,
            Widgets\ActivityFeedWidget::class,
        ];
    }

    public function getGreeting(): string
    {
        return now()->hour < 18 ? 'Bonjour' : 'Bonsoir';
    }

    public function getFirstName(): string
    {
        $name = trim((string) auth()->user()?->name);

        return $name !== '' ? str($name)->before(' ')->toString() : 'Admin';
    }
}
