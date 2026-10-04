<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AcquisitionResource;
use App\Models\Order;
use Filament\Widgets\Widget;

class PendingAcquisitionsWidget extends Widget
{
    protected static ?int $sort = 6;

    protected static string $view = 'filament.widgets.pending-acquisitions';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $slaMinutes = config('concierge.sla_hours') * 60;

        // Oldest first: the request closest to breaching the SLA leads.
        $orders = Order::open()
            ->with(['user:id,name', 'items.watch.images', 'reviewingAdmin:id,name'])
            ->oldest()
            ->limit(5)
            ->get();

        return [
            'orders' => $orders,
            'openCount' => Order::open()->count(),
            'indexUrl' => AcquisitionResource::getUrl('index', ['queue' => 'pending']),
            'reviewUrl' => fn (Order $order): string => AcquisitionResource::reviewUrl($order),
            // mint while comfortably inside the SLA, gold past half-way, rose once breached
            'urgency' => function (Order $order) use ($slaMinutes): string {
                $ratio = $order->created_at->diffInMinutes(now()) / max(1, $slaMinutes);

                return $ratio >= 1 ? 'breached' : ($ratio >= 0.5 ? 'due' : 'fresh');
            },
        ];
    }
}
