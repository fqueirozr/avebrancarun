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
            ['name' => 'Infantil 6-7 anos', 'type' => 'Infantil', 'age_range' => '6 a 7 anos', 'distance' => '100 m', 'sort_order' => 10],
            ['name' => 'Infantil 8-9 anos', 'type' => 'Infantil', 'age_range' => '8 a 9 anos', 'distance' => '200 m', 'sort_order' => 20],
            ['name' => 'Infantil 10-11 anos', 'type' => 'Infantil', 'age_range' => '10 a 11 anos', 'distance' => '300 m', 'sort_order' => 30],
            ['name' => 'Infantil 12-13 anos', 'type' => 'Infantil', 'age_range' => '12 a 13 anos', 'distance' => '400 m', 'sort_order' => 40],
            ['name' => 'Adulto a partir de 14 anos', 'type' => 'Adulto', 'age_range' => 'A partir de 14 anos', 'distance' => '3 km', 'sort_order' => 50],
            ['name' => 'Adulto a partir de 16 anos', 'type' => 'Adulto', 'age_range' => 'A partir de 16 anos', 'distance' => '6 km', 'sort_order' => 60],
        ])->each(fn (array $modality): RaceModality => RaceModality::query()->updateOrCreate(
            ['name' => $modality['name'], 'distance' => $modality['distance']],
            $modality + ['is_active' => true],
        ));
    }
}
