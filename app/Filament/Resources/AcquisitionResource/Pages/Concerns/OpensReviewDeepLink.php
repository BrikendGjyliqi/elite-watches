<?php

namespace App\Filament\Resources\AcquisitionResource\Pages\Concerns;

use App\Filament\Resources\AcquisitionResource;
use App\Models\Order;

/**
 * Opens a request's dossier when the page is reached via ?review={id}
 * (bell notifications, alert emails, dashboard links).
 *
 * Runs as a trait "booted" hook because Livewire calls those after the parent
 * page's traits — i.e. after Filament has built the table the action lives on.
 */
trait OpensReviewDeepLink
{
    public function bootedOpensReviewDeepLink(): void
    {
        if (blank($this->reviewRecord)) {
            return;
        }

        $order = Order::find($this->reviewRecord);
        $this->reviewRecord = null;

        if (! $order) {
            return;
        }

        // The dossier can only open from the queue that actually lists the request.
        $queue = AcquisitionResource::queueFor($order->status);

        if ($queue !== $this->queue) {
            $this->redirect(AcquisitionResource::reviewUrl($order));

            return;
        }

        $this->mountTableAction('review', (string) $order->getKey());
    }
}
