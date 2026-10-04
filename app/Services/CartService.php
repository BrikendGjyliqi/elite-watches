<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\User;
use App\Models\Watch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public const TAX_RATE = 0.18;

    public const FREE_SHIPPING_THRESHOLD = 5000.0;

    public const SHIPPING_FLAT_RATE = 40.0;

    /**
     * The identifying scope (user_id or session_id) for the current cart owner.
     */
    protected function ownerScope(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }

        return ['session_id' => Session::getId()];
    }

    public function items(): Collection
    {
        return CartItem::with(['watch.images', 'watch.brand'])
            ->where($this->ownerScope())
            ->get();
    }

    public function add(Watch $watch, int $quantity = 1): CartItem
    {
        $item = CartItem::firstOrNew([
            ...$this->ownerScope(),
            'watch_id' => $watch->id,
        ]);

        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->save();

        return $item;
    }

    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $item = CartItem::where($this->ownerScope())->findOrFail($cartItemId);

        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function remove(int $cartItemId): void
    {
        CartItem::where($this->ownerScope())->where('id', $cartItemId)->delete();
    }

    public function clear(): void
    {
        CartItem::where($this->ownerScope())->delete();
    }

    public function subtotal(): float
    {
        return $this->items()->sum(
            fn (CartItem $item) => (float) ($item->watch->discount_price ?? $item->watch->price) * $item->quantity
        );
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * @return array{subtotal: float, tax: float, shipping: float, total: float}
     */
    public function calculateTotals(?float $subtotal = null): array
    {
        $subtotal ??= $this->subtotal();

        $shipping = $subtotal > 0 && $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_FLAT_RATE;
        $tax = round($subtotal * self::TAX_RATE, 2);

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $subtotal > 0 ? $shipping : 0.0,
            'total' => round($subtotal + $tax + ($subtotal > 0 ? $shipping : 0), 2),
        ];
    }

    /**
     * Merge a guest session's cart into the given user's cart, combining
     * quantities for any watch already present in both.
     */
    public function mergeSessionIntoUser(string $sessionId, User $user): void
    {
        $guestItems = CartItem::where('session_id', $sessionId)->get();

        foreach ($guestItems as $guestItem) {
            $userItem = CartItem::firstOrNew([
                'user_id' => $user->id,
                'watch_id' => $guestItem->watch_id,
            ]);

            $userItem->quantity = ($userItem->exists ? $userItem->quantity : 0) + $guestItem->quantity;
            $userItem->save();

            $guestItem->delete();
        }
    }
}
