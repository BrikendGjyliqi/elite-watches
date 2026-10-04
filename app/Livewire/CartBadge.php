<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBadge extends Component
{
    #[On('cart-updated')]
    public function refreshBadge(): void
    {
        // No-op: re-rendering is enough, the count is recomputed from the database on every render.
    }

    public function render()
    {
        return view('livewire.cart-badge', [
            'count' => app(CartService::class)->count(),
        ]);
    }
}
