<?php

namespace Tests\Feature;

use App\Filament\Resources\AcquisitionResource;
use App\Filament\Resources\AcquisitionResource\Pages\ListAcquisitions;
use App\Mail\NewAcquisitionAlertMail;
use App\Mail\OrderApprovedMail;
use App\Mail\OrderDeclinedMail;
use App\Mail\OrderInfoRequestMail;
use App\Mail\OrderRequestedMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Watch;
use App\Services\AcquisitionService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AcquisitionFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $client;

    protected User $admin;

    protected Watch $watch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Mail::fake();

        $this->client = User::where('role', 'customer')->firstOrFail();
        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->watch = Watch::where('stock', '>=', 3)->firstOrFail();
    }

    protected function submitRequest(string $method = 'wire', array $overrides = []): Order
    {
        CartItem::create(['user_id' => $this->client->id, 'watch_id' => $this->watch->id, 'quantity' => 1]);

        $response = $this->actingAs($this->client)
            ->post(route('checkout.submit'), [
                'full_name' => 'Elena Marks',
                'phone' => '+41 79 555 0142',
                'street' => '12 Rue du Rhône',
                'city' => 'Geneva',
                'postal_code' => '1204',
                'country' => 'Switzerland',
                'preferred_method' => $method,
                'customer_note' => 'Could the bracelet be sized for a 17cm wrist?',
                'acknowledged' => '1',
                ...$overrides,
            ]);

        $order = Order::latest('id')->firstOrFail();
        $response->assertRedirect(route('checkout.confirmation', $order->order_number));

        return $order;
    }

    // ── Storefront ─────────────────────────────────────────────

    public function test_checkout_page_offers_concierge_preferences_and_no_stripe(): void
    {
        CartItem::create(['user_id' => $this->client->id, 'watch_id' => $this->watch->id, 'quantity' => 1]);

        $this->actingAs($this->client)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Private Acquisition')
            ->assertSee('By appointment · Reviewed personally by our atelier')
            ->assertSee('Wire Transfer')
            ->assertSee('At the Boutique')
            ->assertSee('Financing · 6, 12, or 24 months')
            ->assertSee('Digital Assets')
            ->assertSee('Submit Acquisition Request')
            ->assertDontSee('js.stripe.com')
            ->assertDontSee('card-element');
    }

    public function test_submitting_a_request_creates_a_requested_order_without_touching_stock(): void
    {
        $stockBefore = $this->watch->stock;

        $order = $this->submitRequest('boutique');

        $this->assertSame('requested', $order->status);
        $this->assertSame('boutique', $order->preferred_method);
        $this->assertSame('Could the bracelet be sized for a 17cm wrist?', $order->customer_note);
        $this->assertMatchesRegularExpression('/^ELT-\d{4}-\d{5}$/', $order->order_number);
        $this->assertCount(1, $order->items);
        $this->assertSame($stockBefore, $this->watch->fresh()->stock, 'Stock is only reserved on approval.');
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'to_status' => 'requested']);

        Mail::assertSent(OrderRequestedMail::class, fn ($mail) => $mail->hasTo($this->client->email));
        Mail::assertSent(NewAcquisitionAlertMail::class, fn ($mail) => $mail->hasTo(config('concierge.notification_email')));

        // Every admin gets a bell notification linking straight to the dossier.
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $this->admin->id]);
        $this->assertStringContainsString(
            'review='.$order->id,
            json_encode($this->admin->notifications()->first()->data),
        );
    }

    public function test_admin_bell_alert_is_delivered_without_a_queue_worker(): void
    {
        // Mirrors .env (QUEUE_CONNECTION=database), where nothing may be processing the queue.
        config(['queue.default' => 'database']);

        $order = $this->submitRequest();

        $this->assertSame(1, $this->admin->notifications()->count());
        $this->assertDatabaseCount('jobs', 0);
        $this->assertStringContainsString($order->order_number, json_encode($this->admin->notifications()->first()->data));
    }

    public function test_confirmation_page_acknowledges_the_request(): void
    {
        $order = $this->submitRequest();

        $this->actingAs($this->client)
            ->get(route('checkout.confirmation', $order->order_number))
            ->assertOk()
            ->assertSee('Request received.')
            ->assertSee('#'.$order->order_number)
            ->assertSee('Review by atelier')
            ->assertSee('View my requests')
            ->assertSee('Return to the boutique');
    }

    public function test_request_requires_a_settlement_preference_and_acknowledgement(): void
    {
        CartItem::create(['user_id' => $this->client->id, 'watch_id' => $this->watch->id, 'quantity' => 1]);

        $this->actingAs($this->client)
            ->from(route('checkout.index'))
            ->post(route('checkout.submit'), [
                'full_name' => 'Elena Marks', 'street' => 'x', 'city' => 'x', 'postal_code' => 'x', 'country' => 'x',
                'preferred_method' => 'card',
                'customer_note' => str_repeat('a', 1001),
            ])
            ->assertRedirect(route('checkout.index'))
            ->assertSessionHasErrors(['preferred_method', 'acknowledged', 'customer_note']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_a_client_cannot_ship_to_another_clients_saved_address(): void
    {
        $other = User::where('role', 'customer')->whereKeyNot($this->client->id)->firstOrFail();
        $foreign = $other->addresses()->create([
            'full_name' => 'Someone Else', 'street' => '1 Way', 'city' => 'Bern', 'postal_code' => '3000', 'country' => 'CH',
        ]);

        CartItem::create(['user_id' => $this->client->id, 'watch_id' => $this->watch->id, 'quantity' => 1]);

        $this->actingAs($this->client)
            ->post(route('checkout.submit'), ['address_id' => $foreign->id, 'preferred_method' => 'wire', 'acknowledged' => '1'])
            ->assertSessionHasErrors('address_id');
    }

    // ── Review decisions ───────────────────────────────────────

    public function test_approving_reserves_stock_adjusts_price_and_emails_settlement_instructions(): void
    {
        $order = $this->submitRequest('wire');
        $stockBefore = $this->watch->stock;

        $shortages = app(AcquisitionService::class)->approve($order, $this->admin, adjustedTotal: 9999.5, message: 'A pleasure to reserve this for you.', internalNote: 'Long-standing client.');

        $order->refresh();
        $this->assertSame([], $shortages);
        $this->assertSame('approved', $order->status);
        $this->assertNotNull($order->approved_at);
        $this->assertSame($this->admin->id, $order->reviewed_by);
        $this->assertEquals(9999.50, (float) $order->total);
        $this->assertNotNull($order->original_total);
        $this->assertSame('Long-standing client.', $order->notes);
        $this->assertSame($stockBefore - 1, $this->watch->fresh()->stock);
        $this->assertSame('A pleasure to reserve this for you.', $order->messages()->first()->body);

        Mail::assertSent(OrderApprovedMail::class, function (OrderApprovedMail $mail) {
            $html = $mail->render();

            return $mail->hasTo($this->client->email)
                && str_contains($html, 'Settlement by wire transfer')
                && str_contains($html, 'IBAN')
                && str_contains($html, 'A pleasure to reserve this for you.');
        });
    }

    public function test_each_settlement_method_gets_its_own_instructions(): void
    {
        $expectations = [
            'wire' => ['Settlement by wire transfer', 'IBAN'],
            'boutique' => ['Settlement at the boutique', 'Book your appointment'],
            'financing' => ['Settlement by financing', '6 months · 12 months · 24 months'],
            'crypto' => ['Settlement in digital assets', 'USDT (ERC-20)'],
        ];

        foreach ($expectations as $method => $needles) {
            $order = $this->submitRequest($method);
            app(AcquisitionService::class)->approve($order, $this->admin);

            $html = (new OrderApprovedMail($order->fresh()))->render();

            foreach ($needles as $needle) {
                $this->assertStringContainsString($needle, $html, "Approval email for '{$method}' is missing '{$needle}'.");
            }

            // The client's order page shows the same instructions once approved.
            $this->actingAs($this->client)
                ->get(route('account.orders.show', $order->order_number))
                ->assertSee($needles[0]);
        }
    }

    public function test_approval_never_drives_stock_negative_and_reports_the_shortfall(): void
    {
        $order = $this->submitRequest();
        $this->watch->update(['stock' => 0]);

        $shortages = app(AcquisitionService::class)->approve($order, $this->admin);

        $this->assertSame([$this->watch->name], $shortages);
        $this->assertSame(0, $this->watch->fresh()->stock);
        $this->assertStringContainsString('Re-order required', $order->statusHistories()->latest('id')->first()->note);
    }

    public function test_declining_records_the_reason_and_emails_it_with_alternatives(): void
    {
        $order = $this->submitRequest();
        $stockBefore = $this->watch->stock;

        app(AcquisitionService::class)->decline($order, $this->admin, 'This reference has been allocated to a long-standing client.');

        $order->refresh();
        $this->assertSame('declined', $order->status);
        $this->assertNotNull($order->declined_at);
        $this->assertSame($this->admin->id, $order->reviewed_by);
        $this->assertSame($stockBefore, $this->watch->fresh()->stock);

        Mail::assertSent(OrderDeclinedMail::class, function (OrderDeclinedMail $mail) {
            $html = $mail->render();

            return str_contains($html, 'allocated to a long-standing client')
                && str_contains($html, 'Pieces you may also admire');
        });

        $this->actingAs($this->client)
            ->get(route('account.orders'))
            ->assertSee('allocated to a long-standing client');
    }

    public function test_decline_requires_a_reason(): void
    {
        $this->expectException(DomainException::class);

        app(AcquisitionService::class)->decline($this->submitRequest(), $this->admin, '  ');
    }

    public function test_a_decided_request_cannot_be_decided_again(): void
    {
        $order = $this->submitRequest();
        app(AcquisitionService::class)->decline($order, $this->admin, 'Not available this season.');

        $this->expectException(DomainException::class);

        app(AcquisitionService::class)->approve($order, $this->admin);
    }

    public function test_requesting_info_opens_a_conversation_the_client_can_answer(): void
    {
        $order = $this->submitRequest();

        app(AcquisitionService::class)->requestInfo($order, $this->admin, 'Could you confirm your wrist size?');

        $this->assertSame('under_review', $order->fresh()->status);
        Mail::assertSent(OrderInfoRequestMail::class, fn (OrderInfoRequestMail $mail) => str_contains($mail->render(), 'Could you confirm your wrist size?'));

        $this->actingAs($this->client)
            ->get(route('account.orders.show', $order->order_number))
            ->assertOk()
            ->assertSee('Conversation with the atelier')
            ->assertSee('Could you confirm your wrist size?');

        $this->actingAs($this->client)
            ->post(route('account.orders.reply', $order->order_number), ['body' => 'Seventeen centimetres, thank you.'])
            ->assertRedirect();

        $this->assertDatabaseHas('order_messages', ['order_id' => $order->id, 'author_role' => 'customer', 'body' => 'Seventeen centimetres, thank you.']);
        $this->assertTrue($this->admin->notifications()->get()->contains(fn ($n) => str_contains(json_encode($n->data), 'replied')));

        // The atelier sees the thread in the dossier, and the request can still be approved.
        app(AcquisitionService::class)->approve($order, $this->admin);
        $this->assertSame('approved', $order->fresh()->status);
    }

    public function test_clients_cannot_reply_to_someone_elses_or_a_closed_request(): void
    {
        $order = $this->submitRequest();
        $intruder = User::where('role', 'customer')->whereKeyNot($this->client->id)->firstOrFail();

        $this->actingAs($intruder)
            ->post(route('account.orders.reply', $order->order_number), ['body' => 'Hello'])
            ->assertNotFound();

        app(AcquisitionService::class)->decline($order, $this->admin, 'Not available this season.');

        $this->actingAs($this->client)
            ->post(route('account.orders.reply', $order->order_number), ['body' => 'Hello'])
            ->assertForbidden();
    }

    // ── Account ────────────────────────────────────────────────

    public function test_account_lists_acquisitions_with_status_pills(): void
    {
        $order = $this->submitRequest();

        $this->actingAs($this->client)
            ->get(route('account.orders'))
            ->assertOk()
            ->assertSee('My Acquisitions')
            ->assertSee($order->order_number)
            ->assertSee('Requested');
    }

    // ── Admin review desk ──────────────────────────────────────

    public function test_review_desk_queues_render_with_empty_states(): void
    {
        $this->actingAs($this->admin, 'admin');

        foreach (array_keys(AcquisitionResource::QUEUES) as $queue) {
            $this->get(AcquisitionResource::getUrl('index', ['queue' => $queue]))->assertOk();
        }

        $this->get(AcquisitionResource::getUrl('index', ['queue' => 'pending']))
            ->assertSee('No pending acquisitions · the vault is quiet.');
    }

    public function test_pending_queue_lists_requests_and_deep_link_opens_the_dossier(): void
    {
        $order = $this->submitRequest();

        $this->actingAs($this->admin, 'admin');

        $this->get(AcquisitionResource::getUrl('index', ['queue' => 'pending']))
            ->assertOk()
            ->assertSee($order->order_number);

        Livewire::withQueryParams(['queue' => 'pending', 'review' => (string) $order->id])
            ->test(ListAcquisitions::class)
            ->assertSet('mountedTableActions', ['review'])
            ->assertSee('Approve request')
            ->assertSee('Request more info')
            ->assertSee('Decline request')
            ->assertSee('Could the bracelet be sized for a 17cm wrist?');

        // Opening the dossier marks it as being reviewed and logs the view.
        $this->assertSame($this->admin->id, $order->fresh()->reviewing_by);
        $this->assertDatabaseHas('activity_log', ['subject_id' => $order->id, 'event' => 'viewed', 'causer_id' => $this->admin->id]);
    }

    public function test_dossier_approve_button_runs_the_approval(): void
    {
        $order = $this->submitRequest();
        $this->actingAs($this->admin, 'admin');

        Livewire::test(ListAcquisitions::class, ['queue' => 'pending'])
            ->mountTableAction('review', $order)
            ->set('mountedTableActionsData.0.price_adjustment', '12000')
            // Exactly what the browser does: open Approve's confirmation, then confirm it.
            ->call('mountFormComponentAction', 'mountedTableActionsData.0.approveAction', 'approve')
            ->assertSet('mountedFormComponentActions', ['approve'])
            ->call('callMountedFormComponentAction')
            ->assertHasNoErrors()
            ->assertSet('mountedTableActions', []);

        $order->refresh();
        $this->assertSame('approved', $order->status);
        $this->assertEquals(12000, (float) $order->total);
        Mail::assertSent(OrderApprovedMail::class);
    }

    public function test_admin_pulse_feed_reports_new_requests(): void
    {
        $first = $this->submitRequest();

        // The first poll (since=0) only sets a baseline; nothing is replayed.
        $this->actingAs($this->admin, 'admin')
            ->getJson(route('filament.admin.acquisitions.pulse'))
            ->assertOk()
            ->assertJsonPath('latest', $first->id)
            ->assertJsonCount(0, 'requests');

        $second = $this->submitRequest('crypto');

        $this->actingAs($this->admin, 'admin')
            ->getJson(route('filament.admin.acquisitions.pulse', ['since' => $first->id]))
            ->assertOk()
            ->assertJsonPath('pending', 2)
            ->assertJsonCount(1, 'requests')
            ->assertJsonPath('requests.0.id', $second->id);
    }

    public function test_pulse_feed_is_admin_only(): void
    {
        $this->getJson(route('filament.admin.acquisitions.pulse'))->assertUnauthorized();

        // A storefront client session carries no admin rights.
        $this->actingAs($this->client)
            ->getJson(route('filament.admin.acquisitions.pulse'))
            ->assertUnauthorized();
    }
}
