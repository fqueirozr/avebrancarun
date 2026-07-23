<?php

namespace Database\Factories;

use App\Models\Shirt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shirt>
 */
class ShirtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 20, 100),
            'registration_price' => null,
            'stock_quantity' => fake()->optional()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
