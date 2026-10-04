<?php

namespace App\Services;

use App\Events\ClientRepliedToAtelier;
use App\Events\OrderApproved;
use App\Events\OrderDeclined;
use App\Events\OrderInfoRequested;
use App\Events\OrderRequested;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderMessage;
use App\Models\User;
use App\Models\Watch;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Every state change of an acquisition request goes through here, so the storefront,
 * the admin review panel and the tests all share one set of rules.
 */
class AcquisitionService
{
    /**
     * Turn the client's cart into a request. Stock is NOT reserved here — only on approval.
     *
     * @param  Collection<int, \App\Models\CartItem>  $items
     * @param  array{subtotal: float, tax: float, shipping: float, total: float}  $totals
     */
    public function submit(User $client, Address $address, Collection $items, array $totals, string $method, ?string $note): Order
    {
        throw_if($items->isEmpty(), DomainException::class, 'There is nothing to request.');

        $order = DB::transaction(function () use ($client, $address, $items, $totals, $method, $note): Order {
            $order = Order::create([
                'user_id' => $client->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'requested',
                'preferred_method' => $method,
                'customer_note' => filled($note) ? trim($note) : null,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'shipping' => $totals['shipping'],
                'total' => $totals['total'],
                'shipping_address_id' => $address->id,
            ]);

            foreach ($items as $item) {
                $unitPrice = $item->watch->discount_price ?? $item->watch->price;

                $order->items()->create([
                    'watch_id' => $item->watch_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $item->quantity,
                ]);
            }

            $this->recordTransition($order, null, 'requested', $client, 'Acquisition request submitted by the client.');

            return $order;
        });

        OrderRequested::dispatch($order);

        return $order;
    }

    /**
     * Approve: reserve the pieces, optionally re-price, and hand over to settlement.
     *
     * @return array<int, string> names of pieces that could not be fully reserved from stock
     */
    public function approve(Order $order, User $admin, ?float $adjustedTotal = null, ?string $message = null, ?string $internalNote = null): array
    {
        $shortages = DB::transaction(function () use ($order, $admin, $adjustedTotal, $message, $internalNote): array {
            $order = $this->lockOpen($order);
            $shortages = [];

            foreach ($order->items as $item) {
                /** @var Watch|null $watch */
                $watch = Watch::whereKey($item->watch_id)->lockForUpdate()->first();

                if (! $watch) {
                    continue;
                }

                // Never go below zero: a shortfall means the atelier must re-order the piece.
                $reserved = min($watch->stock, $item->quantity);
                if ($reserved > 0) {
                    $watch->decrement('stock', $reserved);
                }
                if ($reserved < $item->quantity) {
                    $shortages[] = $watch->name;
                }
            }

            $attributes = [
                'status' => 'approved',
                'approved_at' => now(),
                'reviewed_by' => $admin->id,
                'admin_response' => filled($message) ? trim($message) : null,
                'reviewing_by' => null,
                'reviewing_started_at' => null,
            ];

            if ($adjustedTotal !== null && $adjustedTotal > 0 && round($adjustedTotal, 2) !== round((float) $order->total, 2)) {
                $attributes['original_total'] = $order->original_total ?? $order->total;
                $attributes['total'] = round($adjustedTotal, 2);
            }

            if (filled($internalNote)) {
                $attributes['notes'] = trim($internalNote);
            }

            $from = $order->status;
            $order->update($attributes);

            if (filled($message)) {
                $this->addMessage($order, $admin, 'atelier', $message);
            }

            $this->recordTransition($order, $from, 'approved', $admin, $shortages
                ? 'Approved. Re-order required for: '.implode(', ', $shortages).'.'
                : 'Approved. Pieces reserved from stock.');

            return $shortages;
        });

        OrderApproved::dispatch($order->refresh());

        return $shortages;
    }

    public function decline(Order $order, User $admin, string $reason, ?string $internalNote = null): void
    {
        throw_if(blank($reason), DomainException::class, 'A reason is required to decline a request.');

        DB::transaction(function () use ($order, $admin, $reason, $internalNote): void {
            $order = $this->lockOpen($order);
            $from = $order->status;

            $attributes = [
                'status' => 'declined',
                'declined_at' => now(),
                'declined_reason' => trim($reason),
                'reviewed_by' => $admin->id,
                'reviewing_by' => null,
                'reviewing_started_at' => null,
            ];

            if (filled($internalNote)) {
                $attributes['notes'] = trim($internalNote);
            }

            $order->update($attributes);

            $this->recordTransition($order, $from, 'declined', $admin, 'Declined: '.trim($reason));
        });

        OrderDeclined::dispatch($order->refresh());
    }

    public function requestInfo(Order $order, User $admin, string $question, ?string $internalNote = null): OrderMessage
    {
        throw_if(blank($question), DomainException::class, 'Write the question for the client.');

        $message = DB::transaction(function () use ($order, $admin, $question, $internalNote): OrderMessage {
            $order = $this->lockOpen($order);
            $from = $order->status;

            $attributes = ['status' => 'under_review', 'admin_response' => trim($question)];

            if (filled($internalNote)) {
                $attributes['notes'] = trim($internalNote);
            }

            $order->update($attributes);

            if ($from !== 'under_review') {
                $this->recordTransition($order, $from, 'under_review', $admin, 'More information requested from the client.');
            }

            return $this->addMessage($order, $admin, 'atelier', $question);
        });

        OrderInfoRequested::dispatch($order->refresh(), $message);

        return $message;
    }

    /** The client answers the atelier from their order page. */
    public function clientReply(Order $order, User $client, string $body): OrderMessage
    {
        throw_unless($order->user_id === $client->id, DomainException::class, 'This request belongs to another client.');
        throw_unless($order->isOpen(), DomainException::class, 'This request is no longer open for messages.');

        $message = $this->addMessage($order, $client, 'customer', $body);

        ClientRepliedToAtelier::dispatch($order, $message);

        return $message;
    }

    /**
     * Mark that an admin has opened the dossier: logs the view and sets the
     * "being reviewed by" marker unless a colleague holds a fresh one.
     */
    public function markReviewing(Order $order, User $admin): void
    {
        activity()
            ->performedOn($order)
            ->causedBy($admin)
            ->event('viewed')
            ->log('viewed');

        if (! $order->isOpen() || $order->isBeingReviewedByAnotherAdmin($admin->id)) {
            return;
        }

        // Quiet + no timestamp bump: opening a dossier is not an edit of the request.
        Order::withoutTimestamps(fn () => $order->forceFill([
            'reviewing_by' => $admin->id,
            'reviewing_started_at' => now(),
        ])->saveQuietly());
    }

    protected function lockOpen(Order $order): Order
    {
        $locked = Order::with('items')->whereKey($order->getKey())->lockForUpdate()->firstOrFail();

        throw_unless($locked->isOpen(), DomainException::class, "Request {$locked->order_number} has already been {$locked->statusLabel()}.");

        return $locked;
    }

    protected function addMessage(Order $order, User $author, string $role, string $body): OrderMessage
    {
        return $order->messages()->create([
            'user_id' => $author->id,
            'author_role' => $role,
            'body' => trim($body),
        ]);
    }

    protected function recordTransition(Order $order, ?string $from, string $to, User $by, ?string $note = null): void
    {
        $order->statusHistories()->create([
            'from_status' => $from,
            'to_status' => $to,
            'changed_by' => $by->id,
            'note' => $note,
        ]);
    }
}
