<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class PendingReviewsWidget extends Widget
{
    protected static ?int $sort = 10;

    protected static string $view = 'filament.widgets.pending-reviews';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = ['default' => 1, 'md' => 1, 'xl' => 10];

    public function approve(int $reviewId): void
    {
        Review::whereKey($reviewId)->where('is_approved', false)->firstOrFail()->update(['is_approved' => true]);

        Notification::make()->title('Review approved')->body('It is now visible on the watch page.')->success()->send();
    }

    public function reject(int $reviewId): void
    {
        Review::whereKey($reviewId)->where('is_approved', false)->firstOrFail()->delete();

        Notification::make()->title('Review rejected')->body('The review has been removed.')->send();
    }

    protected function getViewData(): array
    {
        return [
            'reviews' => Review::with(['user:id,name', 'watch:id,name'])
                ->where('is_approved', false)
                ->latest()
                ->limit(4)
                ->get(),
        ];
    }
}
