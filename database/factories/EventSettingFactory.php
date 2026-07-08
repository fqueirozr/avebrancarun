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
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->numerify('###########'),
            'contact_whatsapp' => fake()->numerify('###########'),
            'general_information' => fake()->paragraph(),
            'kit_information' => fake()->sentence(),
            'baggage_storage_information' => fake()->paragraph(),
            'start_groups_information' => fake()->paragraph(),
            'timing_information' => fake()->paragraph(),
            'special_registrations_information' => fake()->paragraph(),
            'course_information' => fake()->paragraph(),
            'course_images' => [],
            'regulation' => fake()->paragraphs(3, true),
        ];
    }
}
