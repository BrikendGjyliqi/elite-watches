<?php

namespace App\Listeners;

use App\Events\ClientRepliedToAtelier;
use App\Events\OrderApproved;
use App\Events\OrderDeclined;
use App\Events\OrderInfoRequested;
use App\Events\OrderRequested;
use App\Filament\Resources\AcquisitionResource;
use App\Mail\NewAcquisitionAlertMail;
use App\Mail\OrderApprovedMail;
use App\Mail\OrderDeclinedMail;
use App\Mail\OrderInfoRequestMail;
use App\Mail\OrderRequestedMail;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationSender;
use Throwable;

/**
 * Emails the client and alerts the atelier as a request moves through review.
 * Handlers are auto-discovered by their event type-hints.
 */
class NotifyAcquisitionParties
{
    public function handleRequested(OrderRequested $event): void
    {
        $order = $event->order->loadMissing('user');

        $this->mail($order->user->email, new OrderRequestedMail($order));
        $this->mail(config('concierge.notification_email'), new NewAcquisitionAlertMail($order));

        $this->alertAdmins(
            Notification::make()
                ->title("New acquisition request · {$order->order_number}")
                ->body($order->user->name.' · €'.number_format((float) $order->total, 2).' · '.($order->methodLabel() ?? 'No preference'))
                ->icon('heroicon-o-sparkles')
                ->iconColor('warning')
                ->actions([
                    Action::make('review')
                        ->label('Review')
                        ->button()
                        ->url(AcquisitionResource::reviewUrl($order))
                        ->markAsRead(),
                ]),
        );
    }

    public function handleApproved(OrderApproved $event): void
    {
        $this->mail($event->order->user->email, new OrderApprovedMail($event->order));
    }

    public function handleDeclined(OrderDeclined $event): void
    {
        $this->mail($event->order->user->email, new OrderDeclinedMail($event->order));
    }

    public function handleInfoRequested(OrderInfoRequested $event): void
    {
        $this->mail($event->order->user->email, new OrderInfoRequestMail($event->order, $event->message->body));
    }

    public function handleClientReply(ClientRepliedToAtelier $event): void
    {
        $order = $event->order->loadMissing('user');

        $this->alertAdmins(
            Notification::make()
                ->title("{$order->user->name} replied · {$order->order_number}")
                ->body(str($event->message->body)->limit(140)->toString())
                ->icon('heroicon-o-chat-bubble-left-right')
                ->iconColor('info')
                ->actions([
                    Action::make('review')
                        ->label('Open dossier')
                        ->button()
                        ->url(AcquisitionResource::reviewUrl($order))
                        ->markAsRead(),
                ]),
        );
    }

    /**
     * Bell notification for every admin. Sent now rather than queued: Filament's
     * DatabaseNotification is ShouldQueue, and an alert waiting on a queue worker
     * defeats the point of a 24h SLA.
     */
    protected function alertAdmins(Notification $notification): void
    {
        $admins = User::where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            NotificationSender::sendNow($admins, $notification->toDatabase());
        }
    }

    /** A mail outage must never undo or error a request that is already saved. */
    protected function mail(?string $to, Mailable $mailable): void
    {
        if (blank($to)) {
            return;
        }

        try {
            Mail::to($to)->send($mailable);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
