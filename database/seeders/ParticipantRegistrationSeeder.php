<?php

namespace Database\Seeders;

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Illuminate\Database\Seeder;

class ParticipantRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kits = Kit::query()->get();
        $raceModalities = RaceModality::query()->get();
        ParticipantRegistration::factory()->count(8)->recycle($kits)->recycle($raceModalities)->create();
        ParticipantRegistration::factory()->paid()->count(8)->recycle($kits)->recycle($raceModalities)->create();
        ParticipantRegistration::factory()->cancelled()->count(3)->recycle($kits)->recycle($raceModalities)->create();
        ParticipantRegistration::factory()->count(6)->recycle($kits)->recycle($raceModalities)->create();
    }
}
