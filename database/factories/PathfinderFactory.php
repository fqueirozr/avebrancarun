<?php

namespace Database\Factories;

use App\Models\Pathfinder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pathfinder>
 */
class PathfinderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'code' => fake()->unique()->numerify('####'),
            'is_active' => true,
        ];
    }
}
