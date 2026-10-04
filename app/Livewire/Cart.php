<?php

namespace App\Livewire;

use App\Models\Watch;
use App\Services\CartService;
use Livewire\Component;

class Cart extends Component
{
    public function add(int $watchId, int $quantity = 1): void
    {
        $watch = Watch::findOrFail($watchId);

        app(CartService::class)->add($watch, $quantity);

        $this->dispatch('cart-updated');
    }

    public function updateQty(int $cartItemId, int $quantity): void
    {
        app(CartService::class)->updateQuantity($cartItemId, $quantity);

        $this->dispatch('cart-updated');
    }

    public function remove(int $cartItemId): void
    {
        app(CartService::class)->remove($cartItemId);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: __('Item removed from your cart.'), type: 'info');
    }

    public function clear(): void
    {
        app(CartService::class)->clear();

        $this->dispatch('cart-updated');
    }

    public function getItemsProperty()
    {
        return app(CartService::class)->items();
    }

    public function getTotalsProperty(): array
    {
        return app(CartService::class)->calculateTotals();
    }

    public function getCountProperty(): int
    {
        return app(CartService::class)->count();
    }

    public function render()
    {
        return view('livewire.cart', [
            'items' => $this->items,
            'totals' => $this->totals,
            'count' => $this->count,
        ]);
    }
}
