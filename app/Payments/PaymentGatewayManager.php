<?php

namespace App\Payments;

use App\Models\PaymentGatewaySetting;
use App\Payments\Gateways\AsaasCheckoutGateway;
use RuntimeException;

class PaymentGatewayManager implements PaymentGateway
{
    public function createCheckout(CheckoutRequest $request): CheckoutResponse
    {
        $settings = PaymentGatewaySetting::current();

        if (! $settings->isConfigured()) {
            throw new RuntimeException('Gateway de pagamento nao configurado.');
        }

        return match ($settings->gateway) {
            'asaas' => app(AsaasCheckoutGateway::class)->createCheckout($request, $settings),
            default => throw new RuntimeException("Gateway [{$settings->gateway}] nao suportado."),
        };
    }
}
