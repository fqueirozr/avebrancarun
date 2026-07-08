<?php

namespace Database\Seeders;

use App\Models\EventSetting;
use Illuminate\Database\Seeder;

class EventSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventSetting::query()->firstOrCreate([], [
            'event_date' => 'A confirmar',
            'event_location' => 'A confirmar',
            'kit_information' => 'Em definição',
            'regulation' => 'Em revisão',
        ]);
    }
}
