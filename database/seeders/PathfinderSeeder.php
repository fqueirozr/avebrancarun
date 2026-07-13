<?php

namespace Database\Seeders;

use App\Models\Pathfinder;
use Illuminate\Database\Seeder;

class PathfinderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pathfinder::factory()->count(10)->create();
        Pathfinder::factory()->inactive()->count(2)->create();
    }
}
