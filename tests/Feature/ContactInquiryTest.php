<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactInquiryResource\Pages\ListContactInquiries;
use App\Mail\ContactInquiryAcknowledgementMail;
use App\Mail\ContactInquiryReceivedMail;
use App\Mail\ContactInquiryReplyMail;
use App\Models\ContactInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ContactInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        RateLimiter::clear('contact-inquiry:127.0.0.1');
    }

    /** @return array<string, string> */
    protected function payload(array $overrides = []): array
    {
        return [
            'name' => 'Elena Marks',
            'email' => 'elena@example.com',
            'phone_prefix' => '+41',
            'phone' => '79 555 0142',
            'subject' => 'unlisted',
            'preferred_channel' => 'whatsapp',
            'message' => 'I am searching for a Patek Philippe 5711/1A with full set, ideally 2019 or later.',
            'consent' => '1',
            'website' => '',
            ...$overrides,
        ];
    }

    public function test_contact_page_renders_the_concierge_journey(): void
    {
        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee('<title>Contact · ÉLITE Maison Horlogère</title>', false)
            ->assertSeeInOrder([
                'Reach the atelier.',
                'Three doors into the maison.',
                'Share your inquiry with our atelier.',
                'Prishtina, Kosovo.',
                'What stays in the atelier, stays in the atelier.',
                'Our door is open to those who care for time.',
            ])
            ->assertSee('mailto:'.config('concierge.contact.email'), false)
            ->assertSee('name="website"', false)
            ->assertDontSee('Your message is in flight.');
    }

    public function test_a_valid_inquiry_is_stored_both_emails_are_sent_and_the_overlay_shows(): void
    {
        $response = $this->post(route('contact.store'), $this->payload());

        $inquiry = ContactInquiry::firstOrFail();
        $response->assertRedirect(route('contact.index'))
            ->assertSessionHas('inquiry_reference', $inquiry->reference());

        $this->assertSame('new', $inquiry->status);
        $this->assertSame('+41 79 555 0142', $inquiry->phone);
        $this->assertSame('whatsapp', $inquiry->preferred_channel);
        $this->assertMatchesRegularExpression('/^INQ-\d{4}-\d{5}$/', $inquiry->reference());

        Mail::assertSent(ContactInquiryReceivedMail::class, fn ($mail) => $mail->hasTo(config('concierge.notification_email'))
            && $mail->hasReplyTo('elena@example.com')
            && str_contains($mail->render(), 'Reply directly'));
        Mail::assertSent(ContactInquiryAcknowledgementMail::class, fn ($mail) => $mail->hasTo('elena@example.com')
            && str_contains($mail->render(), 'Expect a reply within'));

        $this->followRedirects($response)
            ->assertSee('Your message is in flight.')
            ->assertSee($inquiry->reference());
    }

    public function test_validation_covers_required_fields_and_channel_needs_a_phone(): void
    {
        $this->from(route('contact.index'))
            ->post(route('contact.store'), $this->payload([
                'name' => '',
                'email' => 'not-an-email',
                'phone' => '',
                'subject' => 'gossip',
                'message' => 'Too short',
                'consent' => '',
            ]))
            ->assertRedirect(route('contact.index'))
            ->assertSessionHasErrors(['name', 'email', 'phone', 'subject', 'message', 'consent']);

        $this->assertDatabaseCount('contact_inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_errors_are_announced_with_aria_describedby(): void
    {
        $this->from(route('contact.index'))->post(route('contact.store'), $this->payload(['email' => 'nope']));

        $this->get(route('contact.index'))
            ->assertSee('aria-describedby="email-error"', false)
            ->assertSee('id="email-error"', false);
    }

    public function test_honeypot_submissions_look_successful_but_store_and_send_nothing(): void
    {
        $this->post(route('contact.store'), $this->payload(['website' => 'http://spam.example']))
            ->assertRedirect(route('contact.index'))
            ->assertSessionHas('inquiry_sent');

        $this->assertDatabaseCount('contact_inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_more_than_three_messages_in_ten_minutes_are_held_back(): void
    {
        foreach (range(1, 3) as $i) {
            $this->post(route('contact.store'), $this->payload())->assertSessionHasNoErrors();
        }

        $this->from(route('contact.index'))
            ->post(route('contact.store'), $this->payload())
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('contact_inquiries', 3);
    }

    public function test_validation_failures_do_not_use_up_the_allowance(): void
    {
        foreach (range(1, 5) as $i) {
            $this->post(route('contact.store'), $this->payload(['message' => 'short']));
        }

        $this->post(route('contact.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertDatabaseCount('contact_inquiries', 1);
    }

    public function test_admin_can_list_reply_to_and_archive_inquiries(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->post(route('contact.store'), $this->payload());
        $inquiry = ContactInquiry::firstOrFail();

        $this->actingAs($admin, 'admin');
        $this->get('/admin/inquiries')->assertOk()->assertSee($inquiry->reference());

        Livewire::test(ListContactInquiries::class)
            ->assertCanSeeTableRecords([$inquiry])
            ->callTableAction('reply', $inquiry, data: ['reply' => 'We have located two examples and will call you on WhatsApp this afternoon.'])
            ->assertHasNoTableActionErrors();

        $inquiry->refresh();
        $this->assertSame('replied', $inquiry->status);
        $this->assertSame($admin->id, $inquiry->replied_by);
        Mail::assertSent(ContactInquiryReplyMail::class, fn ($mail) => $mail->hasTo('elena@example.com')
            && $mail->hasReplyTo(config('concierge.contact.email'))
            && str_contains($mail->render(), 'located two examples'));

        Livewire::test(ListContactInquiries::class)
            ->filterTable('status', 'replied')
            ->callTableAction('archive', $inquiry);

        $this->assertSame('archived', $inquiry->fresh()->status);
    }
}
