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
            'type' => Kit::TypeStandard,
            'rules' => null,
            'is_active' => true,
            'sort_order' => 10,
        ];
    }

    public function forEachType(): static
    {
        return $this
            ->count(count(Kit::typeOptions()))
            ->sequence(
                [
                    'name' => 'Kit Normal',
                    'type' => Kit::TypeStandard,
                    'sort_order' => 10,
                ],
                [
                    'name' => 'Kit PCD / 60+',
                    'type' => Kit::TypePcd60,
                    'rules' => 'Inscrição destinada a pessoas com deficiência ou participantes com 60 anos ou mais.',
                    'sort_order' => 20,
                ],
                [
                    'name' => 'Kit Social',
                    'type' => Kit::TypeSocial,
                    'rules' => 'Inscrição sujeita às regras do kit social.',
                    'sort_order' => 30,
                ],
                [
                    'name' => 'Kit Desbravador',
                    'type' => Kit::TypePathfinder,
                    'rules' => 'Inscrição destinada a participantes do programa Desbravador.',
                    'sort_order' => 40,
                ],
            );
    }
}
