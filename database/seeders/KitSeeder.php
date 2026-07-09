<?php

namespace Database\Seeders;

use App\Models\Kit;
use Illuminate\Database\Seeder;

class KitSeeder extends Seeder
{
    /**
     * Seed the kits.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Kit Ave Branca Run', 'description' => 'Número de peito, medalha e itens definidos pela organização.', 'price' => 50, 'sort_order' => 10],
        ])->each(fn (array $kit): Kit => Kit::query()->updateOrCreate(
            ['name' => $kit['name']],
            $kit + ['is_active' => true],
        ));
    }
}
