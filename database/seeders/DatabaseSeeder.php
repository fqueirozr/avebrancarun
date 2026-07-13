<?php

namespace Database\Seeders;

use App\Models\Kit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //kitfactory form each

        Kit::factory()->forEachType()->create();
        $this->call([
            EventSettingSeeder::class,
            RaceModalitySeeder::class,
            PaymentGatewaySettingSeeder::class,
            UserSeeder::class,
            ContactMessageSeeder::class,
            PathfinderSeeder::class,
            ParticipantRegistrationSeeder::class,
        ]);
    }
}
