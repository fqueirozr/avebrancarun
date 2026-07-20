<?php

namespace Database\Factories;

use App\Models\MailSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MailSetting>
 */
class MailSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mailer' => 'smtp',
            'scheme' => null,
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => fake()->userName(),
            'password' => fake()->password(12),
            'from_address' => fake()->safeEmail(),
            'from_name' => fake()->company(),
        ];
    }
}
