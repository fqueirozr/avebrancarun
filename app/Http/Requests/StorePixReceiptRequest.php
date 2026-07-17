<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePixReceiptRequest extends FormRequest
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
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_document' => ['required', 'string', 'regex:/^\d{11}$/'],
            'pix_receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'payer_data_confirmed' => ['accepted'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @return array<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (filled($this->input('billing_name')) && ! preg_match('/\pL+\s+\pL+/u', (string) $this->input('billing_name'))) {
                    $validator->errors()->add('billing_name', 'Informe o nome completo do pagador.');
                }

                if (filled($this->input('billing_document')) && ! $this->hasValidCpf((string) $this->input('billing_document'))) {
                    $validator->errors()->add('billing_document', 'Informe um CPF válido.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'billing_name.required' => 'Informe o nome completo do pagador.',
            'billing_document.required' => 'Informe o CPF do pagador.',
            'billing_document.regex' => 'Informe um CPF válido.',
            'pix_receipt.required' => 'Envie o comprovante do Pix.',
            'pix_receipt.mimes' => 'O comprovante deve ser uma imagem JPG, PNG ou um PDF.',
            'pix_receipt.max' => 'O comprovante não pode ter mais de 5 MB.',
            'payer_data_confirmed.accepted' => 'Confirme que os dados do recebedor e do pagador foram conferidos.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('billing_document')) {
            return;
        }

        $this->merge([
            'billing_document' => preg_replace('/\D+/', '', (string) $this->input('billing_document')),
        ]);
    }

    private function hasValidCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($position = 0; $position < $digit; $position++) {
                $sum += (int) $cpf[$position] * (($digit + 1) - $position);
            }

            if ((int) $cpf[$digit] !== ((10 * $sum) % 11) % 10) {
                return false;
            }
        }

        return true;
    }
}
