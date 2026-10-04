<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Widgets\Widget;

class RecentCustomersWidget extends Widget
{
    protected static ?int $sort = 11;

    protected static string $view = 'filament.widgets.recent-customers';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 1, 'xl' => 10];

    protected function getViewData(): array
    {
        return [
            'customers' => User::where('role', 'customer')->latest()->limit(5)->get(['id', 'name', 'email', 'created_at']),
            'urlFor' => fn (User $user): string => UserResource::getUrl('edit', ['record' => $user]),
        ];
    }
}
