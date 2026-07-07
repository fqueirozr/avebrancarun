<?php

namespace Database\Factories;

use App\Models\ParticipantRegistration;
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
        $modalities = [
            'Infantil 6-7 anos - 100 m',
            'Infantil 8-9 anos - 200 m',
            'Infantil 10-11 anos - 300 m',
            'Infantil 12-13 anos - 400 m',
            'Adulto a partir de 14 anos - 3 km',
            'Adulto a partir de 16 anos - 6 km',
        ];

        return [
            'athlete_name' => fake()->name(),
            'birth_date' => fake()->dateTimeBetween('-45 years', '-6 years')->format('Y-m-d'),
            'guardian_name' => fake()->optional()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'modality' => fake()->randomElement($modalities),
            'notes' => fake()->optional()->sentence(),
            'payment_status' => 'pending',
        ];
    }
}
