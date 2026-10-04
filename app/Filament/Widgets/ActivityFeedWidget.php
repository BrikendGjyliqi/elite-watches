<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityResource;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityFeedWidget extends Widget
{
    protected static ?int $sort = 12;

    protected static string $view = 'filament.widgets.activity-feed';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 2, 'xl' => 10];

    protected function getViewData(): array
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->whereHasMorph('causer', [User::class], fn (Builder $query) => $query->where('role', 'admin'))
            ->latest()
            ->limit(10)
            ->get();

        return [
            'activities' => $activities,
            'auditUrl' => ActivityResource::getUrl('index'),
            'describe' => fn (Activity $activity): string => ActivityResource::describe($activity),
        ];
    }
}
