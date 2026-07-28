<?php

namespace App\Http\Requests;

use App\Models\ParticipantRegistration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'customer_cpf' => ['required', 'string', 'regex:/^\d{11}$/'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
            'sizes' => ['required', 'array', 'list', 'min:1', 'max:10'],
            'sizes.*' => ['required', Rule::in(array_keys(ParticipantRegistration::shirtSizeOptions()))],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (is_array($this->input('sizes')) && count($this->input('sizes')) !== $this->integer('quantity')) {
                    $validator->errors()->add('sizes', 'Informe o tamanho de cada camiseta selecionada.');
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'sizes' => 'tamanhos',
            'sizes.*' => 'tamanho da camiseta',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['customer_cpf', 'customer_phone'] as $field) {
            $this->merge([
                $field => preg_replace('/\D+/', '', (string) $this->input($field)),
            ]);
        }
    }
}
