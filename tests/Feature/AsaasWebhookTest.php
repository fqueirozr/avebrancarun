<?php

use App\Mail\ParticipantRegistrationUpdated;
use App\Models\ParticipantRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('payments.asaas.webhook_token', 'test-webhook-token');
});

test('asaas payment confirmation marks the registration as paid', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'email' => 'maria@example.com',
        'payment_status' => 'pending',
        'payment_gateway' => 'asaas',
        'payment_gateway_reference' => 'checkout_123',
    ]);

    $this->withHeader('asaas-access-token', 'test-webhook-token')->postJson(route('webhooks.asaas'), [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => [
            'id' => 'pay_123',
            'externalReference' => "participant-registration:{$registration->id}",
        ],
    ])->assertNoContent();

    expect($registration->refresh()->payment_status)->toBe('paid');

    Mail::assertSent(ParticipantRegistrationUpdated::class, function (ParticipantRegistrationUpdated $mail): bool {
        return $mail->hasTo('maria@example.com')
            && $mail->registration->payment_status === 'paid';
    });
});

test('asaas payment confirmation can match by checkout reference', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'payment_gateway' => 'asaas',
        'payment_gateway_reference' => 'checkout_123',
    ]);

    $this->withHeader('asaas-access-token', 'test-webhook-token')->postJson(route('webhooks.asaas'), [
        'event' => 'PAYMENT_RECEIVED',
        'payment' => [
            'id' => 'pay_123',
            'checkoutSession' => 'checkout_123',
        ],
    ])->assertNoContent();

    expect($registration->refresh()->payment_status)->toBe('paid');

    Mail::assertSent(ParticipantRegistrationUpdated::class);
});

test('asaas repeated confirmation does not send another update email', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'paid',
        'payment_gateway' => 'asaas',
        'payment_gateway_reference' => 'checkout_123',
    ]);

    $this->withHeader('asaas-access-token', 'test-webhook-token')->postJson(route('webhooks.asaas'), [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => [
            'externalReference' => "participant-registration:{$registration->id}",
        ],
    ])->assertNoContent();

    expect($registration->refresh()->payment_status)->toBe('paid');

    Mail::assertNothingSent();
});

test('asaas webhook rejects requests without a valid token', function (?string $token) {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'payment_gateway' => 'asaas',
    ]);

    $request = $this;

    if ($token !== null) {
        $request = $request->withHeader('asaas-access-token', $token);
    }

    $request->postJson(route('webhooks.asaas'), [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => [
            'externalReference' => "participant-registration:{$registration->id}",
        ],
    ])->assertUnauthorized();

    expect($registration->refresh()->payment_status)->toBe('pending');

    Mail::assertNothingSent();
})->with([
    'missing token' => null,
    'invalid token' => 'invalid-webhook-token',
]);
