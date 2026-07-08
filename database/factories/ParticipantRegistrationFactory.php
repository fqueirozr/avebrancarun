<?php

namespace Database\Factories;

use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'athlete_name' => fake()->name(),
            'birth_date' => fake()->dateTimeBetween('-45 years', '-6 years')->format('Y-m-d'),
            'participant_cpf' => '52998224725',
            'guardian_name' => fake()->optional()->name(),
            'guardian_cpf' => fake()->optional()->randomElement(['15350946056', '11144477735']),
            'phone' => '11999999999',
            'email' => fake()->safeEmail(),
            'billing_document' => '52998224725',
            'billing_name' => fake()->name(),
            'billing_address' => fake()->streetName(),
            'billing_address_number' => fake()->buildingNumber(),
            'billing_province' => fake()->city(),
            'billing_postal_code' => fake()->numerify('########'),
            'race_modality_id' => fn (): int => RaceModality::query()->value('id') ?? RaceModality::factory()->create()->id,
            'modality' => fn (array $attributes): string => RaceModality::query()
                ->find($attributes['race_modality_id'])
                ->displayName(),
            'notes' => fake()->optional()->sentence(),
            'payment_status' => 'pending',
        ];
    }
}
