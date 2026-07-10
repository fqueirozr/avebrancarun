<?php

namespace Database\Factories;

use App\Models\Kit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kit>
 */
class KitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Pacote Oficial',
            'photo_path' => null,
            'description' => 'Camiseta oficial, medalha para concluintes, número de peito e chip de cronometragem quando aplicável.',
            'price' => 139.90,
            'is_half_registration' => false,
            'is_active' => true,
            'sort_order' => 10,
        ];
    }
}
