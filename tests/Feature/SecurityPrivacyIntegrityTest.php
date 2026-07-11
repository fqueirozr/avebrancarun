<?php

use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('public state-changing forms include a csrf token', function (string $routeName) {
    $this->get(route($routeName))
        ->assertSuccessful()
        ->assertSee('name="_token"', false);
})->with([
    'registration' => 'registration',
    'contact' => 'home',
]);

test('the Asaas webhook remains csrf-exempt but requires its secret token', function () {
    config()->set('payments.asaas.webhook_token', 'webhook-secret');

    $this->postJson(route('webhooks.asaas'), [])
        ->assertUnauthorized();
});

test('private health information is encrypted at rest', function () {
    $registration = ParticipantRegistration::factory()->create([
        'health_notes' => 'Alergia grave a penicilina',
    ]);

    $storedValue = DB::table('participant_registrations')
        ->where('id', $registration->id)
        ->value('health_notes');

    expect($storedValue)
        ->not->toBe('Alergia grave a penicilina')
        ->and($registration->refresh()->health_notes)->toBe('Alergia grave a penicilina');
});

test('payment credentials are encrypted at rest and omitted from serialization', function () {
    $settings = PaymentGatewaySetting::query()->create([
        'gateway' => 'asaas',
        'is_enabled' => true,
        'environment' => 'production',
        'api_key' => 'asaas-production-secret',
        'checkout_minutes_to_expire' => 60,
        'billing_types' => ['PIX'],
        'charge_types' => ['DETACHED'],
    ]);

    $storedValue = DB::table('payment_gateway_settings')
        ->where('id', $settings->id)
        ->value('api_key');

    expect($storedValue)
        ->not->toBe('asaas-production-secret')
        ->and($settings->refresh()->api_key)->toBe('asaas-production-secret')
        ->and($settings->toArray())->not->toHaveKey('api_key');
});

test('unsigned or tampered payment return links cannot alter payment state', function () {
    $registration = ParticipantRegistration::factory()->create(['payment_status' => 'pending']);

    $this->get(route('registration.payment.success', $registration))->assertForbidden();

    $validUrl = URL::temporarySignedRoute(
        'registration.payment.success',
        now()->addMinutes(30),
        ['registration' => $registration],
    );

    $this->get($validUrl.'&registration=999999')->assertForbidden();

    expect($registration->refresh()->payment_status)->toBe('pending');
});

test('expired payment return links cannot alter payment state', function () {
    $registration = ParticipantRegistration::factory()->create(['payment_status' => 'pending']);

    $expiredUrl = URL::temporarySignedRoute(
        'registration.payment.success',
        now()->subMinute(),
        ['registration' => $registration],
    );

    $this->get($expiredUrl)->assertForbidden();

    expect($registration->refresh()->payment_status)->toBe('pending');
});

test('an Asaas webhook cannot pay a registration belonging to another gateway', function () {
    Mail::fake();
    config()->set('payments.asaas.webhook_token', 'webhook-secret');

    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'payment_gateway' => 'another-gateway',
        'payment_gateway_reference' => 'foreign-reference',
    ]);

    $this->withHeader('asaas-access-token', 'webhook-secret')
        ->postJson(route('webhooks.asaas'), [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'externalReference' => "participant-registration:{$registration->id}",
            ],
        ])
        ->assertNoContent();

    expect($registration->refresh()->payment_status)->toBe('pending');
    Mail::assertNothingSent();
});

test('unmatched webhook logging excludes personal and authentication data', function () {
    config()->set('payments.asaas.webhook_token', 'webhook-secret');
    Log::spy();

    $this->withHeader('asaas-access-token', 'webhook-secret')
        ->postJson(route('webhooks.asaas'), [
            'event' => 'PAYMENT_CONFIRMED',
            'customer' => ['email' => 'private@example.com', 'cpfCnpj' => '52998224725'],
            'payment' => ['id' => 'pay_unknown'],
        ])
        ->assertNoContent();

    Log::shouldHaveReceived('warning')->once()->withArgs(
        fn (string $message, array $context): bool => ! str_contains(json_encode([$message, $context]), 'private@example.com')
            && ! str_contains(json_encode([$message, $context]), '52998224725')
            && ! str_contains(json_encode([$message, $context]), 'webhook-secret')
    );
});

test('registration identity is not exposed by model serialization', function () {
    $registration = ParticipantRegistration::factory()->create([
        'participant_cpf' => '52998224725',
    ]);

    expect($registration->toArray())
        ->not->toHaveKey('registration_identity');
});
