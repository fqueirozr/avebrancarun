<?php

namespace App\Models;

use Database\Factories\PaymentGatewaySettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'gateway',
    'is_enabled',
    'manual_pix_enabled',
    'pix_key',
    'environment',
    'api_key',
    'checkout_minutes_to_expire',
    'billing_types',
    'charge_types',
])]
class PaymentGatewaySetting extends Model
{
    /** @use HasFactory<PaymentGatewaySettingFactory> */
    use HasFactory;

    protected $hidden = [
        'api_key',
    ];

    public static function current(): self
    {
        return self::query()->first() ?? new self([
            'gateway' => 'asaas',
            'environment' => 'sandbox',
            'checkout_minutes_to_expire' => 60,
            'billing_types' => ['PIX', 'CREDIT_CARD'],
            'charge_types' => ['DETACHED'],
        ]);
    }

    public function isConfigured(): bool
    {
        return $this->is_enabled && filled($this->api_key);
    }

    public function hasManualPix(): bool
    {
        return $this->manual_pix_enabled && filled($this->pix_key);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'manual_pix_enabled' => 'boolean',
            'api_key' => 'encrypted',
            'billing_types' => 'array',
            'charge_types' => 'array',
        ];
    }
}
