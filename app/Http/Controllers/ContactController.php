<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInquiryRequest;
use App\Mail\ContactInquiryAcknowledgementMail;
use App\Mail\ContactInquiryReceivedMail;
use App\Models\ContactInquiry;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    /** At most this many messages per IP per window (failed validation does not count). */
    protected const MAX_PER_WINDOW = 3;

    protected const WINDOW_SECONDS = 600;

    public function store(ContactInquiryRequest $request): RedirectResponse
    {
        $throttleKey = 'contact-inquiry:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_PER_WINDOW)) {
            $minutes = (int) ceil(RateLimiter::availableIn($throttleKey) / 60);

            return back()
                ->withInput()
                ->withErrors(['message' => "We have received several messages from you just now. Please allow {$minutes} ".str('minute')->plural($minutes).' before writing again — or email us directly.']);
        }

        RateLimiter::hit($throttleKey, self::WINDOW_SECONDS);

        // Honeypot: bots fill the hidden "website" field. Pretend all is well and keep nothing.
        if (filled($request->input('website'))) {
            Log::info('Contact form honeypot triggered.', ['ip' => $request->ip()]);

            return redirect()->route('contact.index')->with('inquiry_sent', true);
        }

        $inquiry = ContactInquiry::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->fullPhone(),
            'subject' => $request->validated('subject'),
            'preferred_channel' => $request->validated('preferred_channel'),
            'message' => $request->validated('message'),
        ]);

        $this->mail(config('concierge.notification_email'), new ContactInquiryReceivedMail($inquiry));
        $this->mail($inquiry->email, new ContactInquiryAcknowledgementMail($inquiry));

        return redirect()
            ->route('contact.index')
            ->with('inquiry_sent', true)
            ->with('inquiry_reference', $inquiry->reference());
    }

    /** The inquiry is already saved; a mail outage must not turn that into an error page. */
    protected function mail(?string $to, Mailable $mailable): void
    {
        if (blank($to)) {
            return;
        }

        try {
            Mail::to($to)->send($mailable);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
