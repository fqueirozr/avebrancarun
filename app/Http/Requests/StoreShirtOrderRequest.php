<?php

namespace App\Http\Requests;

use App\Models\ParticipantRegistration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShirtOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shirt_id' => ['required', Rule::exists('shirts', 'id')->where('is_active', true)],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
            'size' => ['required', Rule::in(array_keys(ParticipantRegistration::shirtSizeOptions()))],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
