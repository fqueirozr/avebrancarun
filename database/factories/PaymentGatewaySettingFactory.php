<?php

namespace Database\Factories;

use App\Models\PaymentGatewaySetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentGatewaySetting>
 */
class PaymentGatewaySettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gateway' => 'asaas',
            'is_enabled' => false,
            'environment' => 'sandbox',
            'api_key' => null,
            'checkout_minutes_to_expire' => 60,
            'billing_types' => ['PIX', 'CREDIT_CARD'],
            'charge_types' => ['DETACHED'],
        ];
    }
}
