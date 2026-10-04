<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Watch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_resource_pages(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin, 'admin');

        $this->get('/admin')->assertOk();
        $this->get('/admin/watches')->assertOk();
        $this->get('/admin/watches/create')->assertOk();
        $this->get('/admin/brands')->assertOk();
        $this->get('/admin/categories')->assertOk();
        $this->get('/admin/reviews')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/orders')->assertOk();

        $watch = Watch::firstOrFail();
        $this->get("/admin/watches/{$watch->id}/edit")->assertOk();

        // Create an order to view.
        $order = Order::create([
            'user_id' => $admin->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => 'requested',
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

        $this->get("/admin/orders/{$order->id}")->assertOk();
    }

    public function test_non_admin_cannot_access_panel(): void
    {
        $this->seed();

        $customer = User::where('role', 'customer')->firstOrFail();

        $this->actingAs($customer, 'admin');

        $this->get('/admin')->assertForbidden();
    }

    public function test_public_routes_resolve(): void
    {
        $this->seed();

        $this->get('/')->assertOk();
        $this->get('/shop')->assertOk();
        $this->get('/brands')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/contact')->assertOk();

        $watch = Watch::firstOrFail();
        $this->get("/shop/{$watch->slug}")->assertOk();

        $brand = $watch->brand;
        $this->get("/brands/{$brand->slug}")->assertOk();
    }
}
