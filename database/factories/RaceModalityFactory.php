<?php

namespace Database\Factories;

use App\Models\RaceModality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RaceModality>
 */
class RaceModalityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ageStart = fake()->numberBetween(6, 16);

        return [
            'name' => fake()->randomElement(['Infantil 6-7 anos', 'Infantil 8-9 anos', 'Adulto a partir de 14 anos']),
            'type' => fake()->randomElement(['Infantil', 'Adulto']),
            'age_start' => $ageStart,
            'age_end' => fake()->optional()->numberBetween($ageStart, 17),
            'distance' => fake()->randomElement(['100 m', '200 m', '3 km', '6 km']),
            'google_maps_embed_url' => fake()->optional()->url(),
            'max_participants' => fake()->optional()->numberBetween(20, 300),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 99),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
