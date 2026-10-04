<?php

namespace App\Filament\Resources\AcquisitionResource\Pages;

use App\Filament\Resources\AcquisitionResource;
use App\Filament\Resources\AcquisitionResource\Pages\Concerns\OpensReviewDeepLink;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

class ListAcquisitions extends ListRecords
{
    use OpensReviewDeepLink;

    protected static string $resource = AcquisitionResource::class;

    /** Which work queue is shown; each sidebar entry links to one. */
    #[Url]
    public string $queue = 'pending';

    /** Deep link: ?review={id} opens that request's dossier on arrival. */
    #[Url(as: 'review')]
    public ?string $reviewRecord = null;

    public function mount(): void
    {
        parent::mount();

        if (! array_key_exists($this->queue, AcquisitionResource::QUEUES)) {
            $this->queue = 'pending';
        }
    }

    public function getTitle(): string | Htmlable
    {
        return AcquisitionResource::QUEUES[$this->queue]['label'];
    }

    public function getSubheading(): string | Htmlable | null
    {
        $count = Order::whereIn('status', AcquisitionResource::QUEUES[$this->queue]['statuses'])->count();

        return match ($this->queue) {
            'pending' => trans_choice('{0} Nothing awaits your review|{1} :count request awaiting your review|[2,*] :count requests awaiting your review', $count),
            default => number_format($count).' '.str('request')->plural($count),
        };
    }

    public function getBreadcrumbs(): array
    {
        return [
            AcquisitionResource::getUrl('index') => 'Acquisitions',
            $this->getTitle(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('desktopAlerts')
                ->label('Desktop alerts')
                ->icon('heroicon-o-bell-alert')
                ->color('gray')
                ->tooltip('Get a desktop notification when a new request arrives while you are signed in.')
                ->alpineClickHandler(<<<'JS'
                    if (! ('Notification' in window)) { alert('This browser does not support desktop notifications.'); return; }
                    Notification.requestPermission().then((permission) => {
                        if (permission === 'granted') new Notification('ÉLITE · Desktop alerts on', { body: 'You will be notified of new acquisition requests.', icon: '/images/logo/elite-mark.svg' });
                    });
                JS),
        ];
    }
}
