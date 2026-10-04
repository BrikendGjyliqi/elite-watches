<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;

class OrderStatusBreakdownWidget extends Widget
{
    protected static ?int $sort = 7;

    protected static string $view = 'filament.widgets.order-status-breakdown';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 2, 'xl' => 10];

    /** Nine statuses read better as six stages of the acquisition journey. */
    protected const STAGES = [
        'review' => ['label' => 'Awaiting review', 'statuses' => ['requested', 'under_review']],
        'approved' => ['label' => 'Approved · awaiting payment', 'statuses' => ['approved', 'awaiting_payment']],
        'paid' => ['label' => 'Paid', 'statuses' => ['paid']],
        'shipped' => ['label' => 'Shipped', 'statuses' => ['shipped']],
        'delivered' => ['label' => 'Delivered', 'statuses' => ['delivered']],
        'closed' => ['label' => 'Declined · cancelled', 'statuses' => ['declined', 'cancelled']],
    ];

    protected function getViewData(): array
    {
        $counts = Order::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $total = (int) $counts->sum();

        $statuses = collect(self::STAGES)->map(function (array $stage, string $key) use ($counts, $total): array {
            $count = (int) collect($stage['statuses'])->sum(fn (string $status) => $counts[$status] ?? 0);

            return [
                'key' => $key,
                'label' => $stage['label'],
                'count' => $count,
                'percent' => $total > 0 ? round($count / $total * 100, 1) : 0,
            ];
        })->values();

        return ['statuses' => $statuses, 'total' => $total];
    }
}
