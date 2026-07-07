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
        return [
            'name' => fake()->randomElement(['Infantil 6-7 anos', 'Infantil 8-9 anos', 'Adulto a partir de 14 anos']),
            'type' => fake()->randomElement(['Infantil', 'Adulto']),
            'age_range' => fake()->randomElement(['6 a 7 anos', '8 a 9 anos', 'A partir de 14 anos']),
            'distance' => fake()->randomElement(['100 m', '200 m', '3 km', '6 km']),
            'price' => fake()->optional()->randomFloat(2, 20, 120),
            'max_participants' => fake()->optional()->numberBetween(20, 300),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 99),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
