<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterParticipantRequest extends FormRequest
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
            'athlete_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'modality' => [
                'required',
                'string',
                Rule::in([
                    'Infantil 6-7 anos - 100 m',
                    'Infantil 8-9 anos - 200 m',
                    'Infantil 10-11 anos - 300 m',
                    'Infantil 12-13 anos - 400 m',
                    'Adulto a partir de 14 anos - 3 km',
                    'Adulto a partir de 16 anos - 6 km',
                ]),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
