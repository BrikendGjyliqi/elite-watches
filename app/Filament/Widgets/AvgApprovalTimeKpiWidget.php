<?php

namespace App\Filament\Widgets;

use App\Models\Order;

class AvgApprovalTimeKpiWidget extends KpiWidget
{
    protected static ?int $sort = 5;

    protected function getKpi(): array
    {
        $sla = config('concierge.sla_hours');

        $recent = static::averageMinutes(now()->subDays(90), now());
        $prior = static::averageMinutes(now()->subDays(180), now()->subDays(90));

        return [
            'label' => 'Avg · Approval time',
            'value' => $recent ?? 0,
            'display' => $recent === null ? '—' : static::humanise($recent),
            'money' => false,
            'sub' => match (true) {
                $recent === null => 'No approvals in the last 90 days',
                $recent <= $sla * 60 => "Under {$sla}h SLA",
                default => "Over {$sla}h SLA",
            },
            'trend' => $recent !== null && $prior !== null ? static::trend($recent, $prior) : null,
            'lowerIsBetter' => true,
            'icon' => 'heroicon-o-clock',
        ];
    }

    /** Mean minutes from submission to approval for requests approved in the window. */
    protected static function averageMinutes($from, $to): ?float
    {
        $durations = Order::query()
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$from, $to])
            ->get(['created_at', 'approved_at'])
            ->map(fn (Order $order): float => max(0, $order->created_at->diffInMinutes($order->approved_at)));

        return $durations->isEmpty() ? null : (float) $durations->avg();
    }

    protected static function humanise(float $minutes): string
    {
        $minutes = (int) round($minutes);

        return match (true) {
            $minutes < 60 => "{$minutes}m",
            $minutes < 1440 => intdiv($minutes, 60).'h '.str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT).'m',
            default => intdiv($minutes, 1440).'d '.intdiv($minutes % 1440, 60).'h',
        };
    }
}
