<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^\d{10,11}$/'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Informe :attribute.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max' => 'O campo :attribute nÃ£o pode ter mais de :max caracteres.',
            'email.email' => 'Informe um e-mail vÃ¡lido.',
            'phone.regex' => 'Informe um telefone vÃ¡lido.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'o nome',
            'email' => 'o e-mail',
            'phone' => 'o telefone',
            'subject' => 'o assunto',
            'message' => 'a mensagem',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('phone')) {
            return;
        }

        $digits = preg_replace('/\D+/', '', (string) $this->input('phone'));

        $this->merge([
            'phone' => $digits === '' ? null : $digits,
        ]);
    }
}
