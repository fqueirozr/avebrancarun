<?php

namespace Database\Factories;

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ParticipantRegistration>
 */
class ParticipantRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $participantCpf = $this->validCpf();

        return [
            'protocol_number' => 'AVR-'.Str::ulid(),
            'athlete_name' => fake()->name(),
            'birth_date' => fake()->dateTimeBetween('1980-01-01', '2000-09-20')->format('Y-m-d'),
            'sex' => fake()->randomElement(array_keys(ParticipantRegistration::sexOptions())),
            'participant_cpf' => $participantCpf,
            'guardian_name' => null,
            'guardian_cpf' => null,
            'filled_by_legal_representative' => false,
            'phone' => '11999999999',
            'email' => fake()->safeEmail(),
            'billing_document' => $participantCpf,
            'billing_name' => fake()->name(),
            'billing_address' => fake()->streetName(),
            'billing_address_number' => fake()->buildingNumber(),
            'billing_province' => fake()->city(),
            'billing_postal_code' => fake()->numerify('########'),
            'race_modality_id' => RaceModality::factory(),
            'kit_id' => Kit::factory(),
            'modality' => fn (array $attributes): string => RaceModality::query()
                ->find($attributes['race_modality_id'])
                ->displayName(),
            'notes' => fake()->optional()->sentence(),
            'emergency_contact_name' => fake()->optional()->name(),
            'emergency_contact_phone' => fake()->optional()->numerify('119########'),
            'health_notes' => fake()->optional()->sentence(),
            'regulation_accepted_at' => now(),
            'regulation_version' => hash('sha256', 'Test regulation'),
            'regulation_acceptance_ip' => fake()->ipv4(),
            'regulation_acceptance_user_agent' => fake()->userAgent(),
            'privacy_policy_accepted_at' => now(),
            'privacy_policy_version' => ParticipantRegistration::PrivacyPolicyVersion,
            'privacy_policy_acceptance_ip' => fake()->ipv4(),
            'privacy_policy_acceptance_user_agent' => fake()->userAgent(),
            'data_confirmation_accepted_at' => now(),
            'data_confirmation_acceptance_ip' => fake()->ipv4(),
            'data_confirmation_acceptance_user_agent' => fake()->userAgent(),
            'special_kit_rules_accepted_at' => null,
            'special_kit_rules_version' => null,
            'special_kit_rules_acceptance_ip' => null,
            'special_kit_rules_acceptance_user_agent' => null,
            'payment_status' => 'pending',
        ];
    }

    private function validCpf(): string
    {
        $cpf = fake()->unique()->numerify('#########');

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($position = 0; $position < $digit; $position++) {
                $sum += (int) $cpf[$position] * (($digit + 1) - $position);
            }

            $cpf .= (string) (((10 * $sum) % 11) % 10);
        }

        return $cpf;
    }
}
