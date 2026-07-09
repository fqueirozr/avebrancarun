<?php

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use App\Models\RaceModality;
use App\Payments\CheckoutRequest;
use App\Payments\Gateways\AsaasCheckoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('asaas checkout gateway creates a checkout session', function () {
    Http::fake([
        'api-sandbox.asaas.com/v3/checkouts' => Http::response([
            'id' => 'checkout_123',
            'link' => 'https://sandbox.asaas.com/checkoutSession/show/checkout_123',
            'status' => 'ACTIVE',
        ]),
    ]);

    $settings = PaymentGatewaySetting::query()->create([
        'gateway' => 'asaas',
        'is_enabled' => true,
        'environment' => 'sandbox',
        'api_key' => 'test-key',
        'checkout_minutes_to_expire' => 60,
        'billing_types' => ['PIX', 'CREDIT_CARD'],
        'charge_types' => ['DETACHED'],
    ]);

    $raceModality = RaceModality::factory()->create([
        'name' => 'Adulto',
        'distance' => '6 km',
    ]);
    $kit = Kit::factory()->create([
        'name' => 'Kit Corrida',
        'price' => 25,
    ]);

    $registration = ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'billing_document' => '52998224725',
        'billing_name' => 'Maria Silva',
        'billing_address' => 'Rua das Flores',
        'billing_address_number' => '123',
        'billing_province' => 'Centro',
        'billing_postal_code' => '70000000',
        'phone' => '(11) 99999-9999',
        'race_modality_id' => $raceModality->id,
    ]);

    $checkout = (new AsaasCheckoutGateway)->createCheckout(new CheckoutRequest(
        registration: $registration,
        raceModality: $raceModality,
        kit: $kit,
        successUrl: 'https://example.com/success',
        cancelUrl: 'https://example.com/cancel',
        expiredUrl: 'https://example.com/expired',
    ), $settings);

    expect($checkout->gateway)->toBe('asaas')
        ->and($checkout->reference)->toBe('checkout_123')
        ->and($checkout->checkoutUrl)->toBe('https://sandbox.asaas.com/checkoutSession/show/checkout_123');

    Http::assertSent(function ($request): bool {
        return $request->hasHeader('access_token', 'test-key')
            && $request->url() === 'https://api-sandbox.asaas.com/v3/checkouts'
            && $request['billingTypes'] === ['PIX', 'CREDIT_CARD']
            && $request['chargeTypes'] === ['DETACHED']
            && $request['customerData']['name'] === 'Maria Silva'
            && $request['customerData']['cpfCnpj'] === '52998224725'
            && $request['customerData']['phone'] === '11999999999'
            && $request['customerData']['address'] === 'Rua das Flores'
            && $request['customerData']['addressNumber'] === '123'
            && $request['customerData']['province'] === 'Centro'
            && $request['customerData']['postalCode'] === '70000000'
            && $request['items'][0]['value'] === 25.0;
    });
});

test('asaas checkout gateway retries with credit card when pix key is missing', function () {
    Http::fakeSequence()
        ->push([
            'errors' => [
                [
                    'code' => 'invalid_object',
                    'description' => 'Para gerar cobrancas com Pix e necessario criar uma chave Pix no Asaas.',
                ],
            ],
        ], 400)
        ->push([
            'id' => 'checkout_card_123',
            'link' => 'https://sandbox.asaas.com/checkoutSession/show/checkout_card_123',
            'status' => 'ACTIVE',
        ]);

    $settings = PaymentGatewaySetting::query()->create([
        'gateway' => 'asaas',
        'is_enabled' => true,
        'environment' => 'sandbox',
        'api_key' => 'test-key',
        'checkout_minutes_to_expire' => 60,
        'billing_types' => ['PIX', 'CREDIT_CARD'],
        'charge_types' => ['DETACHED'],
    ]);

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

    $registration = ParticipantRegistration::factory()->create([
        'race_modality_id' => $raceModality->id,
    ]);

    $checkout = (new AsaasCheckoutGateway)->createCheckout(new CheckoutRequest(
        registration: $registration,
        raceModality: $raceModality,
        kit: $kit,
        successUrl: 'https://example.com/success',
        cancelUrl: 'https://example.com/cancel',
        expiredUrl: 'https://example.com/expired',
    ), $settings);

    expect($checkout->reference)->toBe('checkout_card_123');

    Http::assertSentCount(2);
    Http::assertSent(function ($request): bool {
        return $request['billingTypes'] === ['CREDIT_CARD'];
    });
});

test('asaas checkout gateway applies the legal senior discount for participants older than 65', function () {
    Http::fake([
        'api-sandbox.asaas.com/v3/checkouts' => Http::response([
            'id' => 'checkout_senior_123',
            'link' => 'https://sandbox.asaas.com/checkoutSession/show/checkout_senior_123',
            'status' => 'ACTIVE',
        ]),
    ]);

    $settings = PaymentGatewaySetting::query()->create([
        'gateway' => 'asaas',
        'is_enabled' => true,
        'environment' => 'sandbox',
        'api_key' => 'test-key',
        'checkout_minutes_to_expire' => 60,
        'billing_types' => ['PIX', 'CREDIT_CARD'],
        'charge_types' => ['DETACHED'],
    ]);

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 80]);

    $registration = ParticipantRegistration::factory()->create([
        'birth_date' => today()->subYears(66)->subDay()->format('Y-m-d'),
        'race_modality_id' => $raceModality->id,
    ]);

    (new AsaasCheckoutGateway)->createCheckout(new CheckoutRequest(
        registration: $registration,
        raceModality: $raceModality,
        kit: $kit,
        successUrl: 'https://example.com/success',
        cancelUrl: 'https://example.com/cancel',
        expiredUrl: 'https://example.com/expired',
    ), $settings);

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://api-sandbox.asaas.com/v3/checkouts'
            && $request['items'][0]['value'] === 40.0;
    });
});
