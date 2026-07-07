<?php

namespace Database\Factories;

use App\Models\EventSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventSetting>
 */
class EventSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_date' => fake()->date('d/m/Y'),
            'event_location' => fake()->city(),
            'kit_information' => fake()->sentence(),
            'regulation' => fake()->paragraphs(3, true),
        ];
    }
}
