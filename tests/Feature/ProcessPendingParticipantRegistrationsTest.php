<?php

use App\Mail\ParticipantRegistrationUpdated;
use App\Mail\PendingRegistrationPaymentReminder;
use App\Models\ParticipantRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('it reminds registrations pending for more than four days only once', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'email' => 'atleta@example.com',
        'payment_status' => 'pending',
        'created_at' => now()->subDays(5),
    ]);

    $this->artisan('registrations:process-pending-payments')->assertSuccessful();
    $this->artisan('registrations:process-pending-payments')->assertSuccessful();

    expect($registration->refresh()->payment_reminder_sent_at)->not->toBeNull()
        ->and($registration->payment_status)->toBe('pending');

    Mail::assertSent(PendingRegistrationPaymentReminder::class, function (PendingRegistrationPaymentReminder $mail): bool {
        return $mail->hasTo('atleta@example.com')
            && str_contains($mail->paymentUrl, "/inscricao/{$mail->registration->id}/pagamento");
    });
    Mail::assertSentCount(1);
});

test('it cancels registrations still pending for seven days and sends a friendly email', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'email' => 'atleta@example.com',
        'payment_status' => 'pending',
        'created_at' => now()->subDays(7),
    ]);

    $this->artisan('registrations:process-pending-payments')->assertSuccessful();

    expect($registration->refresh()->payment_status)->toBe('cancelled')
        ->and($registration->cancellation_source)->toBe(ParticipantRegistration::CancellationSourceAutomatic);

    Mail::assertSent(ParticipantRegistrationUpdated::class, function (ParticipantRegistrationUpdated $mail): bool {
        return $mail->hasTo('atleta@example.com')
            && $mail->updateTitle === 'Inscrição cancelada';
    });
    Mail::assertNotSent(PendingRegistrationPaymentReminder::class);
});

test('it leaves recent or resolved registrations unchanged', function () {
    Mail::fake();

    $recent = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'created_at' => now()->subDays(3),
    ]);
    $paid = ParticipantRegistration::factory()->paid()->create([
        'created_at' => now()->subDays(8),
    ]);

    $this->artisan('registrations:process-pending-payments')->assertSuccessful();

    expect($recent->refresh()->payment_status)->toBe('pending')
        ->and($recent->payment_reminder_sent_at)->toBeNull()
        ->and($paid->refresh()->payment_status)->toBe('paid');

    Mail::assertNothingSent();
});

test('payment reminder email explains the deadline and contains the payment action', function () {
    $registration = ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
        'payment_status' => 'pending',
    ]);

    $mail = new PendingRegistrationPaymentReminder(
        $registration,
        'https://example.com/pagamento',
    );

    $mail->assertHasSubject('Lembrete de pagamento da inscrição - Ave Branca Run');
    $mail->assertSeeInHtml('Maria Silva');
    $mail->assertSeeInHtml('checkout on-line ou a uma inscrição criada em fluxo anterior');
    $mail->assertSeeInHtml('7 dias após o cadastro');
    $mail->assertSeeInHtml('Realizar pagamento');
});

test('payment update emails distinguish confirmed and under review registrations', function () {
    $paidMail = new ParticipantRegistrationUpdated(
        ParticipantRegistration::factory()->paid()->create(),
    );
    $underReviewMail = new ParticipantRegistrationUpdated(
        ParticipantRegistration::factory()->create(['payment_status' => 'under_review']),
    );

    $paidMail->assertSeeInHtml('O pagamento foi confirmado e sua inscrição está confirmada');
    $underReviewMail->assertSeeInHtml('Seu comprovante foi recebido e permanece em conferência')
        ->assertSeeInHtml('ainda não confirma o pagamento');
});

test('automatic cancellation email explains the missing payment information amicably', function () {
    $registration = ParticipantRegistration::factory()->cancelled()->create([
        'athlete_name' => 'Maria Silva',
        'cancellation_source' => ParticipantRegistration::CancellationSourceAutomatic,
    ]);

    $mail = new ParticipantRegistrationUpdated($registration, 'Inscrição cancelada');

    $mail->assertSeeInHtml('não identificamos informações de pagamento');
    $mail->assertSeeInHtml('Sabemos que imprevistos acontecem');
    $mail->assertDontSeeInHtml('cancelada pela organização');
});

test('organization cancellation email requests contact for further clarification', function () {
    $registration = ParticipantRegistration::factory()->cancelled()->create([
        'athlete_name' => 'Maria Silva',
    ]);

    $mail = new ParticipantRegistrationUpdated($registration, 'Inscrição cancelada');

    $mail->assertSeeInHtml('Informamos que sua inscrição foi cancelada');
    $mail->assertSeeInHtml('Caso tenha dúvidas ou precise de maiores esclarecimentos');
    $mail->assertDontSeeInHtml('não identificamos informações de pagamento');
});

test('a regular cancellation is classified as made by the organization', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
    ]);

    $registration->update(['payment_status' => 'cancelled']);

    expect($registration->cancellation_source)->toBe(ParticipantRegistration::CancellationSourceOrganization);
});
