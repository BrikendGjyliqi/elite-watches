<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Watch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrderDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Regarding your recent request · ÉLITE');
    }

    public function content(): Content
    {
        $this->order->loadMissing(['user', 'items.watch.brand']);

        return new Content(view: 'emails.acquisitions.declined', with: [
            'alternatives' => $this->alternatives(),
        ]);
    }

    /**
     * Three in-stock pieces close to what was requested: same brand or category first,
     * then nearest in price.
     *
     * @return Collection<int, Watch>
     */
    protected function alternatives(): Collection
    {
        $requested = $this->order->items->pluck('watch')->filter();

        if ($requested->isEmpty()) {
            return collect();
        }

        $anchorPrice = (float) $requested->avg(fn (Watch $watch) => $watch->discount_price ?? $watch->price);

        return Watch::query()
            ->with('brand:id,name')
            ->whereNotIn('id', $requested->pluck('id'))
            ->where('stock', '>', 0)
            ->get()
            ->sortBy([
                fn (Watch $a, Watch $b) => $this->affinity($b, $requested) <=> $this->affinity($a, $requested),
                fn (Watch $a, Watch $b) => abs(($a->discount_price ?? $a->price) - $anchorPrice) <=> abs(($b->discount_price ?? $b->price) - $anchorPrice),
            ])
            ->take(3)
            ->values();
    }

    /** @param  Collection<int, Watch>  $requested */
    protected function affinity(Watch $watch, Collection $requested): int
    {
        return ($requested->contains('brand_id', $watch->brand_id) ? 2 : 0)
            + ($requested->contains('category_id', $watch->category_id) ? 1 : 0);
    }
}
