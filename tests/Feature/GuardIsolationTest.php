<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_and_admin_sessions_are_independent(): void
    {
        $this->seed();

        $customer = User::where('role', 'customer')->firstOrFail();
        $admin = User::where('role', 'admin')->firstOrFail();

        // Log in as a customer on the main site (web guard).
        $this->actingAs($customer, 'web');

        // Visiting the admin login page should not be blocked just because
        // a customer session exists on the unrelated "web" guard.
        $this->get('/admin/login')->assertOk();

        // Logging in as admin (admin guard) must not disturb the customer's
        // web-guard session.
        $this->actingAs($admin, 'admin');

        $this->assertTrue(auth('web')->check());
        $this->assertEquals($customer->id, auth('web')->id());
        $this->assertTrue(auth('admin')->check());
        $this->assertEquals($admin->id, auth('admin')->id());

        $this->get('/admin')->assertOk();
    }
}
