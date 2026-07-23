<?php

namespace App\Http\Requests;

use App\Models\EventSetting;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use App\Models\PaymentGatewaySetting;
use App\Models\RaceModality;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
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
        $kitHasShirt = (bool) Kit::query()
            ->whereKey($this->input('kit_id'))
            ->value('has_shirt');
        $requiresCheckoutData = PaymentGatewaySetting::current()->isConfigured();

        return [
            'athlete_name' => ['required', 'string', 'max:255'],
            'shirt_size' => [
                Rule::excludeIf(! $kitHasShirt),
                'required',
                Rule::in(array_keys(ParticipantRegistration::shirtSizeOptions())),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'sex' => ['required', Rule::in(array_keys(ParticipantRegistration::sexOptions()))],
            'participant_cpf' => ['required', 'string', 'regex:/^\d{11}$/'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_cpf' => ['nullable', 'string', 'regex:/^\d{11}$/'],
            'filled_by_legal_representative' => ['required', 'boolean'],
            'phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
            'email' => ['required', 'email', 'max:255'],
            'billing_document' => [Rule::requiredIf($requiresCheckoutData), 'nullable', 'string', 'regex:/^\d{11}(\d{3})?$/'],
            'billing_name' => [Rule::requiredIf($requiresCheckoutData), 'nullable', 'string', 'max:255'],
            'billing_address' => [Rule::requiredIf($requiresCheckoutData), 'nullable', 'string', 'max:255'],
            'billing_address_number' => [Rule::requiredIf($requiresCheckoutData), 'nullable', 'string', 'max:20'],
            'billing_province' => [Rule::requiredIf($requiresCheckoutData), 'nullable', 'string', 'max:255'],
            'billing_postal_code' => [Rule::requiredIf($requiresCheckoutData), 'nullable', 'string', 'regex:/^\d{8}$/'],
            'race_modality_id' => ['required', Rule::exists('race_modalities', 'id')->where('is_active', true)],
            'kit_id' => ['required', Rule::exists('kits', 'id')->where('is_active', true)],
            'shirt_id' => ['nullable', Rule::exists('shirts', 'id')->where('is_active', true)],
            'extra_shirt_size' => [Rule::requiredIf($this->filled('shirt_id')), 'nullable', Rule::in(array_keys(ParticipantRegistration::shirtSizeOptions()))],
            'extra_shirt_quantity' => [Rule::requiredIf($this->filled('shirt_id')), 'nullable', 'integer', 'min:1', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'regex:/^\d{10,11}$/'],
            'accepted_regulation' => ['accepted'],
            'accepted_privacy_policy' => ['accepted'],
            'accepted_fitness_declaration' => ['accepted'],
            'accepted_data_confirmation' => ['accepted'],
            'accepted_special_kit_rules' => ['nullable', 'boolean'],
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
            'race_modality_id.exists' => 'Escolha uma prova ativa.',
            'kit_id.exists' => 'Escolha um pacote ativo.',
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
            'shirt_size' => 'o tamanho da camisa',
            'birth_date' => 'a data de nascimento',
            'sex' => 'o sexo',
            'participant_cpf' => 'o CPF do atleta',
            'guardian_name' => 'o nome do responsável legal',
            'guardian_cpf' => 'o CPF do responsável legal',
            'phone' => 'o telefone',
            'email' => 'o e-mail',
            'billing_document' => 'o CPF ou CNPJ do pagador',
            'billing_name' => 'o nome completo do pagador',
            'billing_address' => 'o endereço do pagador',
            'billing_address_number' => 'o número do endereço',
            'billing_province' => 'o bairro do pagador',
            'billing_postal_code' => 'o CEP do pagador',
            'race_modality_id' => 'a prova',
            'kit_id' => 'o pacote',
            'emergency_contact_name' => 'o nome do contato de emergência',
            'emergency_contact_phone' => 'o telefone do contato de emergência',
            'accepted_regulation' => 'o Regulamento',
            'accepted_privacy_policy' => 'a Política de Privacidade',
            'accepted_fitness_declaration' => 'a declaração de aptidão para participar da prova',
            'accepted_special_kit_rules' => 'as regras para PCD, 60+ e Meia Social',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $kit = Kit::query()->find($this->input('kit_id'));
                $eventSetting = EventSetting::current();
                $raceModality = RaceModality::query()->find($this->input('race_modality_id'));

                if ($eventSetting->registrationDeadlineHasPassed()) {
                    $validator->errors()->add('registration', 'O prazo para inscrições foi encerrado.');
                } elseif ($eventSetting->registrationLimitHasBeenReached()) {
                    $validator->errors()->add('registration', 'O limite total de inscrições foi atingido.');
                }

                if (filled($this->input('participant_cpf')) && ParticipantRegistration::query()->where('participant_cpf', $this->input('participant_cpf'))->exists()) {
                    $validator->errors()->add('participant_cpf', 'Este atleta já possui uma inscrição.');
                }

                if ($raceModality?->participantLimitHasBeenReached()) {
                    $validator->errors()->add('race_modality_id', 'O limite de inscrições desta prova foi atingido.');
                }

                if ($raceModality !== null && filled($this->input('birth_date')) && strtotime((string) $this->input('birth_date')) !== false) {
                    $birthDate = Carbon::parse((string) $this->input('birth_date'));

                    if (! $raceModality->acceptsBirthDate($birthDate, $eventSetting->eventDateForAgeCalculation())) {
                        $validator->errors()->add('birth_date', "A idade do atleta na data da prova não atende à faixa etária: {$raceModality->ageRangeLabel()}.");
                    }
                }

                if (filled($this->input('participant_cpf')) && ! $this->hasValidCpf((string) $this->input('participant_cpf'))) {
                    $validator->errors()->add('participant_cpf', 'Informe um CPF válido para o atleta.');
                }

                if (filled($this->input('guardian_cpf')) && ! $this->hasValidCpf((string) $this->input('guardian_cpf'))) {
                    $validator->errors()->add('guardian_cpf', 'Informe um CPF válido para o responsável legal.');
                }

                if ($this->requiresLegalRepresentative()) {
                    if (blank($this->input('guardian_name'))) {
                        $validator->errors()->add('guardian_name', 'Informe o nome do representante legal.');
                    }

                    if (blank($this->input('guardian_cpf'))) {
                        $validator->errors()->add('guardian_cpf', 'Informe o CPF do representante legal.');
                    }
                }

                if ($kit?->requiresRulesAcknowledgement() && ! $this->boolean('accepted_special_kit_rules')) {
                    $validator->errors()->add('accepted_special_kit_rules', 'Você precisa ler e declarar ciência das regras deste pacote.');
                }

                if ($kit?->type === Kit::TypePathfinder && filled($this->input('participant_cpf'))) {
                    $pathfinder = Pathfinder::query()
                        ->where('cpf', $this->input('participant_cpf'))
                        ->where('is_active', true)
                        ->first();

                    if ($pathfinder === null) {
                        $validator->errors()->add('participant_cpf', 'Este CPF não está habilitado para o pacote de desbravadores.');
                    } elseif ($pathfinder->registration()->exists()) {
                        $validator->errors()->add('participant_cpf', 'Este desbravador já possui uma inscrição.');
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
        foreach (['participant_cpf', 'guardian_cpf', 'phone', 'emergency_contact_phone', 'billing_document', 'billing_postal_code'] as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $digits = preg_replace('/\D+/', '', (string) $this->input($field));

            $this->merge([
                $field => $digits === '' && $field !== 'participant_cpf' && $field !== 'phone' ? null : $digits,
            ]);
        }

        $this->merge([
            'filled_by_legal_representative' => $this->isMinorParticipant()
                || $this->boolean('filled_by_legal_representative'),
        ]);
    }

    private function isMinorParticipant(): bool
    {
        if (blank($this->input('birth_date'))) {
            return false;
        }

        $birthDateValue = (string) $this->input('birth_date');

        if (strtotime($birthDateValue) === false) {
            return false;
        }

        $birthDate = Carbon::parse($birthDateValue);

        return $birthDate->isAfter(today()->subYears(18));
    }

    private function requiresLegalRepresentative(): bool
    {
        return $this->isMinorParticipant() || $this->boolean('filled_by_legal_representative');
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
