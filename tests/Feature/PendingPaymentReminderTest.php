<?php

use App\Jobs\SendPendingPaymentReminder;
use App\Mail\PendingRegistrationPaymentReminder;
use App\Mail\PendingShirtOrderPaymentReminder;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

it('only sends the pending registration reminder after sixty minutes', function () {
    $registration = ParticipantRegistration::factory()->create([
        'kit_id' => Kit::factory()->create(['price' => 100]),
        'payment_status' => 'pending',
        'payment_checkout_url' => 'https://checkout.example/registration',
        'created_at' => now()->subMinutes(59),
    ]);

    $this->artisan('payments:send-pending-reminders')->assertSuccessful();
    Mail::assertNothingSent();

    $registration->forceFill(['created_at' => now()->subMinutes(61)])->saveQuietly();
    $this->artisan('payments:send-pending-reminders')->assertSuccessful();

    Mail::assertSent(PendingRegistrationPaymentReminder::class, 1);
});

it('emails the athlete page when a registration remains pending', function () {
    $registration = ParticipantRegistration::factory()->create([
        'kit_id' => Kit::factory()->create(['price' => 100]),
        'payment_status' => 'pending',
        'payment_checkout_url' => 'https://checkout.example/registration',
    ]);
    $job = SendPendingPaymentReminder::forRegistration($registration);

    $job->handle();
    $job->handle();

    Mail::assertSent(PendingRegistrationPaymentReminder::class, 1);
    Mail::assertSent(PendingRegistrationPaymentReminder::class, function (PendingRegistrationPaymentReminder $mail) use ($registration): bool {
        return $mail->hasTo($registration->email)
            && str_contains($mail->paymentUrl, "/atleta/{$registration->id}");
    });
    expect($registration->refresh()->payment_reminder_sent_at)->not->toBeNull();
});

it('does not email a reminder when the registration is no longer pending', function () {
    $registration = ParticipantRegistration::factory()->paid()->create([
        'kit_id' => Kit::factory()->create(['price' => 100]),
    ]);

    SendPendingPaymentReminder::forRegistration($registration)->handle();

    Mail::assertNothingSent();
});

it('emails the payment link when a standalone item remains pending', function () {
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'participant_registration_id' => null,
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
        'payment_checkout_url' => 'https://checkout.example/item',
    ]);

    SendPendingPaymentReminder::forShirtOrder($shirtOrder)->handle();

    Mail::assertSent(PendingShirtOrderPaymentReminder::class, function (PendingShirtOrderPaymentReminder $mail): bool {
        return $mail->hasTo('maria@example.com')
            && $mail->paymentUrl === 'https://checkout.example/item';
    });
    expect($shirtOrder->refresh()->payment_reminder_sent_at)->not->toBeNull();
});

it('does not schedule a duplicate reminder for an item linked to a registration', function () {
    $registration = ParticipantRegistration::factory()->create();
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'participant_registration_id' => $registration->id,
        'customer_name' => $registration->athlete_name,
        'customer_email' => $registration->email,
        'customer_phone' => $registration->phone,
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    $shirtOrder->forceFill(['created_at' => now()->subMinutes(61)])->saveQuietly();
    $this->artisan('payments:send-pending-reminders')->assertSuccessful();

    Mail::assertNothingSent();
});
