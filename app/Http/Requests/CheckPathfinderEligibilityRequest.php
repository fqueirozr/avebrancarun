<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckPathfinderEligibilityRequest extends FormRequest
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
            'cpf' => ['required', 'string', 'regex:/^\d{11}$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => preg_replace('/\D+/', '', (string) $this->input('cpf')),
        ]);
    }
}
