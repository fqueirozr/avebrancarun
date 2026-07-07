<?php

namespace Database\Seeders;

use App\Models\ParticipantRegistration;
use App\Models\User;
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
        $this->call(EventSettingSeeder::class);
        $this->call(RaceModalitySeeder::class);

        User::factory(10)->create();

        ParticipantRegistration::factory(15)->create();
    }
}
