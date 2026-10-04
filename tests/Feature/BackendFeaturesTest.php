<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Livewire\Cart;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Models\Watch;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class BackendFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_livewire_cart_add_update_remove_and_subtotal(): void
    {
        $this->seed();

        $user = User::where('role', 'customer')->firstOrFail();
        $watch = Watch::firstOrFail();

        $this->actingAs($user);

        Livewire::test(Cart::class)
            ->call('add', $watch->id, 2)
            ->assertSet('count', 2);

        $item = CartItem::where('user_id', $user->id)->where('watch_id', $watch->id)->firstOrFail();

        $expectedSubtotal = (float) ($watch->discount_price ?? $watch->price) * 2;
        $this->assertEqualsWithDelta($expectedSubtotal, (float) app(\App\Services\CartService::class)->subtotal(), 0.01);

        Livewire::test(Cart::class)
            ->call('updateQty', $item->id, 1)
            ->assertSet('count', 1);

        Livewire::test(Cart::class)
            ->call('remove', $item->id);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_guest_cart_merges_into_user_cart_on_login(): void
    {
        $this->seed();

        $watch = Watch::firstOrFail();
        $user = User::where('role', 'customer')->firstOrFail();

        // Simulate a guest cart tied to the current test session id.
        CartItem::create([
            'session_id' => session()->getId(),
            'watch_id' => $watch->id,
            'quantity' => 3,
        ]);

        $this->actingAs($user);
        event(new \Illuminate\Auth\Events\Login('web', $user, false));

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'watch_id' => $watch->id,
            'quantity' => 3,
        ]);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_wishlist_toggle_adds_and_removes(): void
    {
        $this->seed();

        $user = User::where('role', 'customer')->firstOrFail();
        $watch = Watch::firstOrFail();

        $this->actingAs($user)
            ->post("/wishlist/{$watch->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', ['user_id' => $user->id, 'watch_id' => $watch->id]);

        $this->actingAs($user)
            ->post("/wishlist/{$watch->id}/toggle")
            ->assertRedirect();

        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_authenticated_user_can_submit_a_review_pending_approval(): void
    {
        $this->seed();

        $user = User::where('role', 'customer')->firstOrFail();
        $watch = Watch::firstOrFail();

        $this->actingAs($user)
            ->post("/shop/{$watch->slug}/reviews", [
                'rating' => 5,
                'title' => 'Excellent',
                'body' => 'Exactly as described, fast shipping.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'watch_id' => $watch->id,
            'is_approved' => false,
        ]);
    }

    public function test_order_status_update_logs_history_and_emails_customer(): void
    {
        $this->seed();
        Mail::fake();

        $admin = User::where('role', 'admin')->firstOrFail();
        $customer = User::where('role', 'customer')->firstOrFail();
        $watch = Watch::firstOrFail();

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => 'approved',
            'subtotal' => 100,
            'tax' => 8,
            'shipping' => 0,
            'total' => 108,
        ]);
        $order->items()->create([
            'watch_id' => $watch->id,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
        ]);

        $this->actingAs($admin);

        Livewire::test(ViewOrder::class, ['record' => $order->getKey()])
            ->callAction('updateStatus', data: ['status' => 'paid', 'note' => 'Payment confirmed manually.']);

        $this->assertEquals('paid', $order->fresh()->status);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => 'approved',
            'to_status' => 'paid',
        ]);

        Mail::assertSent(OrderStatusUpdatedMail::class);
    }

    public function test_wishlist_index_requires_authentication(): void
    {
        $this->get('/wishlist')->assertRedirect('/login');
    }
}
