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
            'name' => 'Adulto a partir de 16 anos',
            'type' => 'Adulto',
            'age_start' => 16,
            'age_end' => null,
            'distance' => '6 km',
            'race_date' => '2026-09-20',
            'race_time' => '07:30:00',
            'google_maps_embed_url' => 'https://www.google.com/maps?q=Taguaparque%20Taguatinga%20DF&output=embed',
            'course_information' => 'Tempo máximo de prova: 1h30.',
            'course_images' => [],
            'max_participants' => null,
            'is_active' => true,
            'sort_order' => 60,
            'description' => 'Corrida ou caminhada para participantes a partir de 16 anos.',
        ];
    }
}
