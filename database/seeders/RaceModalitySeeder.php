<?php

namespace Database\Seeders;

use App\Models\RaceModality;
use Illuminate\Database\Seeder;

class RaceModalitySeeder extends Seeder
{
    /**
     * Seed the race modalities.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Infantil 6-7 anos', 'type' => 'Infantil', 'age_start' => 6, 'age_end' => 7, 'distance' => '100 m', 'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed', 'sort_order' => 10],
            ['name' => 'Infantil 8-9 anos', 'type' => 'Infantil', 'age_start' => 8, 'age_end' => 9, 'distance' => '200 m', 'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed', 'sort_order' => 20],
            ['name' => 'Infantil 10-11 anos', 'type' => 'Infantil', 'age_start' => 10, 'age_end' => 11, 'distance' => '300 m', 'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed', 'sort_order' => 30],
            ['name' => 'Infantil 12-13 anos', 'type' => 'Infantil', 'age_start' => 12, 'age_end' => 13, 'distance' => '400 m', 'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed', 'sort_order' => 40],
            ['name' => 'Adulto a partir de 14 anos', 'type' => 'Adulto', 'age_start' => 14, 'age_end' => null, 'distance' => '3 km', 'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed', 'sort_order' => 50],
            ['name' => 'Adulto a partir de 16 anos', 'type' => 'Adulto', 'age_start' => 16, 'age_end' => null, 'distance' => '6 km', 'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed', 'sort_order' => 60],
        ])->each(fn (array $modality): RaceModality => RaceModality::query()->updateOrCreate(
            ['name' => $modality['name'], 'distance' => $modality['distance']],
            $modality + ['is_active' => true],
        ));
    }
}
