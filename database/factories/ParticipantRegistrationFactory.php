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
            'guardian_name' => fake()->optional()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'race_modality_id' => fn (): int => RaceModality::query()->value('id') ?? RaceModality::factory()->create()->id,
            'modality' => fn (array $attributes): string => RaceModality::query()
                ->find($attributes['race_modality_id'])
                ->displayName(),
            'notes' => fake()->optional()->sentence(),
            'payment_status' => 'pending',
        ];
    }
}
