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
            'manual_pix_enabled' => false,
            'pix_key' => null,
            'pix_receiver_name' => null,
            'pix_receiver_city' => null,
            'pix_bank' => null,
            'pix_agency' => null,
            'pix_account' => null,
            'pix_account_holder' => null,
            'environment' => 'sandbox',
            'api_key' => null,
            'checkout_minutes_to_expire' => 60,
            'billing_types' => ['PIX', 'CREDIT_CARD'],
            'charge_types' => ['DETACHED'],
        ];
    }
}
