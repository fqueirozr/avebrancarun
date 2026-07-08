<?php

namespace Database\Seeders;

use App\Models\PaymentGatewaySetting;
use Illuminate\Database\Seeder;

class PaymentGatewaySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentGatewaySetting::query()->firstOrCreate([], [
            'gateway' => 'asaas',
            'is_enabled' => false,
            'environment' => 'sandbox',
            'checkout_minutes_to_expire' => 60,
            'billing_types' => ['PIX', 'CREDIT_CARD'],
            'charge_types' => ['DETACHED'],
        ]);
    }
}
