<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 5;

    protected static string $view = 'filament.widgets.revenue-chart';

    protected static ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 2, 'xl' => 20];

    public ?string $filter = '30d';

    /** @var array<string, array{label: string, days: int}> */
    public const RANGES = [
        '7d' => ['label' => 'Last 7 days', 'days' => 7],
        '30d' => ['label' => 'Last 30 days', 'days' => 30],
        '90d' => ['label' => 'Last 90 days', 'days' => 90],
        '1y' => ['label' => 'Last 12 months', 'days' => 365],
    ];

    protected ?Collection $orders = null;

    public function setRange(string $range): void
    {
        if (array_key_exists($range, self::RANGES)) {
            $this->filter = $range;
            $this->orders = null;
        }
    }

    protected function getFilters(): ?array
    {
        return collect(self::RANGES)->map(fn (array $range): string => $range['label'])->all();
    }

    public function getRangeLabel(): string
    {
        return self::RANGES[$this->filter]['label'] ?? self::RANGES['30d']['label'];
    }

    public function getRangeTotal(): float
    {
        return (float) $this->orders()->sum('total');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function orders(): Collection
    {
        // Grouped in PHP so the query stays portable across MySQL/SQLite.
        return $this->orders ??= Order::revenue()
            ->where('created_at', '>=', $this->rangeStart())
            ->get(['created_at', 'total']);
    }

    protected function rangeStart(): CarbonInterface
    {
        if ($this->filter === '1y') {
            return now()->subMonths(11)->startOfMonth();
        }

        return now()->subDays((self::RANGES[$this->filter]['days'] ?? 30) - 1)->startOfDay();
    }

    protected function getData(): array
    {
        $isYear = $this->filter === '1y';

        $period = $isYear
            ? CarbonPeriod::create($this->rangeStart(), '1 month', now()->startOfMonth())
            : CarbonPeriod::create($this->rangeStart(), '1 day', now()->startOfDay());

        $keyFormat = $isYear ? 'Y-m' : 'Y-m-d';

        $totals = $this->orders()
            ->groupBy(fn (Order $order): string => $order->created_at->format($keyFormat))
            ->map(fn (Collection $orders): float => round((float) $orders->sum('total'), 2));

        $labels = [];
        $values = [];

        foreach ($period as $date) {
            $labels[] = $date->format($isYear ? 'M' : 'j M');
            $values[] = $totals[$date->format($keyFormat)] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Revenue',
                'data' => $values,
                'borderColor' => '#D9B08D',
                'borderWidth' => 1.5,
                'fill' => 'origin',
                'tension' => 0.35,
                'pointRadius' => 0,
                'pointHoverRadius' => 4,
                'pointHoverBackgroundColor' => '#D9B08D',
                'pointHoverBorderColor' => '#1a2124',
            ]],
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                datasets: {
                    line: {
                        backgroundColor: (context) => {
                            const { ctx, chartArea } = context.chart;
                            if (!chartArea) return 'rgba(217, 176, 141, 0.15)';
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(217, 176, 141, 0.3)');
                            gradient.addColorStop(1, 'rgba(217, 176, 141, 0)');
                            return gradient;
                        },
                    },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#232c2e',
                        borderColor: 'rgba(217, 176, 141, 0.45)',
                        borderWidth: 1,
                        cornerRadius: 2,
                        padding: 12,
                        displayColors: false,
                        titleColor: 'rgba(209, 232, 226, 0.7)',
                        titleFont: { family: 'Inter', size: 11, weight: '400' },
                        bodyColor: '#D9B08D',
                        bodyFont: { family: 'Playfair Display', size: 15 },
                        callbacks: {
                            label: (item) => '€' + Number(item.parsed.y).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                        },
                    },
                },
                scales: {
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: { color: 'rgba(209, 232, 226, 0.5)', font: { family: 'Inter', size: 10 }, maxRotation: 0, autoSkipPadding: 18 },
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: 'rgba(209, 232, 226, 0.05)' },
                        ticks: {
                            color: 'rgba(209, 232, 226, 0.5)',
                            font: { family: 'Inter', size: 10 },
                            maxTicksLimit: 5,
                            callback: (value) => '€' + Number(value).toLocaleString('en-US', { notation: 'compact' }),
                        },
                    },
                },
            }
        JS);
    }
}
