<?php

namespace App\Http\Requests;

use App\Models\ContactInquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')) ?: null,
            'phone_prefix' => trim((string) $this->input('phone_prefix', '+383')) ?: '+383',
            'message' => trim((string) $this->input('message')),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone_prefix' => ['nullable', 'string', 'regex:/^\+\d{1,4}$/'],
            // A phone number is required when the client asks to be called or messaged.
            'phone' => ['nullable', 'required_if:preferred_channel,phone,whatsapp', 'string', 'max:30', 'regex:/^[0-9 ()\-\.]{5,}$/'],
            'subject' => ['required', Rule::in(array_keys(ContactInquiry::SUBJECTS))],
            'preferred_channel' => ['required', Rule::in(array_keys(ContactInquiry::CHANNELS))],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'string'], // honeypot, checked in the controller
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name.',
            'email.required' => 'Please share an email address so we can reply.',
            'email.email' => 'That email address does not look quite right.',
            'phone.required_if' => 'Please add a phone number so we can reach you by your chosen channel.',
            'phone.regex' => 'Please enter digits only (spaces are fine).',
            'phone_prefix.regex' => 'Please use a country code such as +383.',
            'subject.required' => 'Please choose what your message is about.',
            'message.required' => 'Please write your message.',
            'message.min' => 'Please share a little more — at least 20 characters.',
            'message.max' => 'Your message can be up to 2,000 characters.',
            'consent.accepted' => 'Please confirm that we may contact you about this inquiry.',
        ];
    }

    /** Full international number, e.g. "+383 44 100 200", or null. */
    public function fullPhone(): ?string
    {
        $phone = $this->validated('phone');

        return $phone ? trim($this->validated('phone_prefix', '+383').' '.$phone) : null;
    }
}
