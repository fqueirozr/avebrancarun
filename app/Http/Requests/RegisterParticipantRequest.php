<?php

namespace App\Http\Requests;

use App\Models\RaceModality;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'participant_cpf' => ['required', 'string', 'regex:/^\d{11}$/'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_cpf' => ['nullable', 'string', 'regex:/^\d{11}$/'],
            'phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
            'email' => ['required', 'email', 'max:255'],
            'billing_document' => ['nullable', 'string', 'regex:/^\d{11}(\d{3})?$/'],
            'billing_name' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_address_number' => ['nullable', 'string', 'max:20'],
            'billing_province' => ['nullable', 'string', 'max:255'],
            'billing_postal_code' => ['nullable', 'string', 'regex:/^\d{8}$/'],
            'race_modality_id' => ['required', Rule::exists('race_modalities', 'id')->where('is_active', true)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'accepted_regulation' => ['accepted'],
            'accepted_privacy_policy' => ['accepted'],
            'accepted_fitness_declaration' => ['accepted'],
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
            'max' => 'O campo :attribute não pode ter mais de :max caracteres.',
            'date' => 'Informe uma data válida para :attribute.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'email.email' => 'Informe um e-mail válido.',
            'regex' => 'Informe um valor válido para :attribute.',
            'race_modality_id.exists' => 'Escolha uma modalidade ativa.',
            'accepted' => 'Você precisa aceitar :attribute.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'athlete_name' => 'o nome do atleta',
            'birth_date' => 'a data de nascimento',
            'participant_cpf' => 'o CPF do participante',
            'guardian_name' => 'o nome do responsável',
            'guardian_cpf' => 'o CPF do responsável',
            'phone' => 'o telefone',
            'email' => 'o e-mail',
            'billing_document' => 'o CPF ou CNPJ do pagador',
            'billing_name' => 'o nome completo do pagador',
            'billing_address' => 'o endereço do pagador',
            'billing_address_number' => 'o número do endereço',
            'billing_province' => 'o bairro do pagador',
            'billing_postal_code' => 'o CEP do pagador',
            'race_modality_id' => 'a modalidade',
            'notes' => 'as observações',
            'accepted_regulation' => 'o Regulamento',
            'accepted_privacy_policy' => 'a Política de Privacidade',
            'accepted_fitness_declaration' => 'a declaração de aptidão para participar da prova',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $raceModality = RaceModality::query()->find($this->input('race_modality_id'));

                if (filled($this->input('participant_cpf')) && ! $this->hasValidCpf((string) $this->input('participant_cpf'))) {
                    $validator->errors()->add('participant_cpf', 'Informe um CPF válido para o participante.');
                }

                if (filled($this->input('guardian_cpf')) && ! $this->hasValidCpf((string) $this->input('guardian_cpf'))) {
                    $validator->errors()->add('guardian_cpf', 'Informe um CPF válido para o responsável.');
                }

                if (! $this->requiresCheckoutData($raceModality)) {
                    return;
                }

                foreach ($this->requiredCheckoutFields() as $field => $message) {
                    if (blank($this->input($field))) {
                        $validator->errors()->add($field, $message);
                    }
                }

                if (filled($this->input('billing_name')) && ! preg_match('/\pL+\s+\pL+/u', (string) $this->input('billing_name'))) {
                    $validator->errors()->add('billing_name', 'Informe o nome completo do pagador.');
                }

                if (filled($this->input('billing_document')) && ! $this->hasValidBillingDocument((string) $this->input('billing_document'))) {
                    $validator->errors()->add('billing_document', 'Informe um CPF ou CNPJ válido.');
                }
            },
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        foreach (['participant_cpf', 'guardian_cpf', 'phone', 'billing_document', 'billing_postal_code'] as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $digits = preg_replace('/\D+/', '', (string) $this->input($field));

            $this->merge([
                $field => $digits === '' && $field !== 'participant_cpf' && $field !== 'phone' ? null : $digits,
            ]);
        }
    }

    private function requiresCheckoutData(?RaceModality $raceModality): bool
    {
        return $raceModality !== null && $raceModality->price !== null && (float) $raceModality->price > 0;
    }

    /**
     * @return array<string, string>
     */
    private function requiredCheckoutFields(): array
    {
        return [
            'billing_document' => 'Informe o CPF ou CNPJ para seguir ao checkout.',
            'billing_name' => 'Informe o nome completo do pagador para seguir ao checkout.',
            'billing_address' => 'Informe o endereço do pagador para seguir ao checkout.',
            'billing_address_number' => 'Informe o número do endereço para seguir ao checkout.',
            'billing_province' => 'Informe o bairro do pagador para seguir ao checkout.',
            'billing_postal_code' => 'Informe o CEP do pagador para seguir ao checkout.',
        ];
    }

    private function hasValidBillingDocument(string $document): bool
    {
        return match (strlen($document)) {
            11 => $this->hasValidCpf($document),
            14 => $this->hasValidCnpj($document),
            default => false,
        };
    }

    private function hasValidCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($position = 0; $position < $digit; $position++) {
                $sum += (int) $cpf[$position] * (($digit + 1) - $position);
            }

            $expectedDigit = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$digit] !== $expectedDigit) {
                return false;
            }
        }

        return true;
    }

    private function hasValidCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $multipliers = [
            [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
            [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
        ];

        foreach ($multipliers as $index => $weights) {
            $sum = 0;

            foreach ($weights as $position => $weight) {
                $sum += (int) $cnpj[$position] * $weight;
            }

            $remainder = $sum % 11;
            $expectedDigit = $remainder < 2 ? 0 : 11 - $remainder;

            if ((int) $cnpj[12 + $index] !== $expectedDigit) {
                return false;
            }
        }

        return true;
    }
}
