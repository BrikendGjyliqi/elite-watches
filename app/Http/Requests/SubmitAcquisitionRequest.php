<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitAcquisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $newAddress = 'required_without:address_id';

        return [
            'address_id' => [
                'nullable',
                Rule::exists('addresses', 'id')->where('user_id', $this->user()->id),
            ],
            'full_name' => [$newAddress, 'nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'street' => [$newAddress, 'nullable', 'string', 'max:255'],
            'city' => [$newAddress, 'nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => [$newAddress, 'nullable', 'string', 'max:50'],
            'country' => [$newAddress, 'nullable', 'string', 'max:255'],

            'preferred_method' => ['required', Rule::in(Order::METHODS)],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'acknowledged' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'preferred_method.required' => 'Please choose how you would prefer to settle.',
            'preferred_method.in' => 'Please choose one of the settlement options.',
            'acknowledged.accepted' => 'Please confirm you understand this is an acquisition request.',
            'customer_note.max' => 'Your note can be up to 1,000 characters.',
            'address_id.exists' => 'Please choose one of your saved addresses.',
            '*.required_without' => 'Please complete your shipping details.',
        ];
    }
}
