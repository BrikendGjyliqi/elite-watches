<?php

namespace App\Livewire;

use App\Models\Watch;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public Watch $watch;

    public int $quantity = 1;

    public function mount(Watch $watch): void
    {
        $this->watch = $watch;
    }

    public function increment(): void
    {
        $this->quantity = min($this->quantity + 1, max((int) $this->watch->stock, 1));
    }

    public function decrement(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function add(CartService $cart): void
    {
        if ($this->watch->stock < 1) {
            return;
        }

        $cart->add($this->watch, $this->quantity);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: "{$this->watch->name} added to your cart.", type: 'success');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
