<?php

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Mail\ParticipantRegistrationReceived;
use App\Mail\ParticipantRegistrationUpdated;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use App\Models\RaceModality;
use App\Models\User;
use App\Payments\CheckoutRequest;
use App\Payments\CheckoutResponse;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('a participant can submit a registration', function () {
    Mail::fake();

    $raceModality = RaceModality::factory()->create([
        'name' => 'Adulto a partir de 16 anos',
        'type' => 'Adulto',
        'age_range' => 'A partir de 16 anos',
        'distance' => '6 km',
        'price' => null,
    ]);

    $registration = ParticipantRegistration::factory()->make([
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'race_modality_id' => $raceModality->id,
        'modality' => $raceModality->displayName(),
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => '529.982.247-25',
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => '153.509.460-56',
        'phone' => '(11) 99999-9999',
        'email' => $registration->email,
        'billing_document' => $registration->billing_document,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertRedirectToRoute('registration')
        ->assertSessionHas('status');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'participant_cpf' => '52998224725',
        'guardian_cpf' => '15350946056',
        'phone' => '11999999999',
        'billing_document' => $registration->billing_document,
        'race_modality_id' => $raceModality->id,
        'modality' => 'Adulto a partir de 16 anos - 6 km',
        'payment_status' => 'pending',
    ]);

    Mail::assertSent(ParticipantRegistrationReceived::class, function (ParticipantRegistrationReceived $mail) {
        return $mail->hasTo('maria@example.com')
            && $mail->registration->athlete_name === 'Maria Silva'
            && $mail->registration->modality === 'Adulto a partir de 16 anos - 6 km';
    });
});

test('a participant is redirected to checkout when payment gateway is configured', function () {
    Mail::fake();

    PaymentGatewaySetting::query()->create([
        'gateway' => 'asaas',
        'is_enabled' => true,
        'environment' => 'sandbox',
        'api_key' => 'test-key',
        'checkout_minutes_to_expire' => 60,
        'billing_types' => ['PIX', 'CREDIT_CARD'],
        'charge_types' => ['DETACHED'],
    ]);

    $raceModality = RaceModality::factory()->create([
        'price' => 25,
    ]);

    $this->app->bind(PaymentGateway::class, fn (): PaymentGateway => new class implements PaymentGateway
    {
        public function createCheckout(CheckoutRequest $request): CheckoutResponse
        {
            return new CheckoutResponse(
                gateway: 'fake',
                reference: 'checkout_123',
                checkoutUrl: 'https://checkout.example/checkout_123',
            );
        }
    });

    $registration = ParticipantRegistration::factory()->make([
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => $registration->participant_cpf,
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => $registration->guardian_cpf,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'billing_document' => '529.982.247-25',
        'billing_name' => 'Maria Silva',
        'billing_address' => 'Rua das Flores',
        'billing_address_number' => '123',
        'billing_province' => 'Centro',
        'billing_postal_code' => '70000-000',
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertRedirect('https://checkout.example/checkout_123');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'email' => $registration->email,
        'payment_gateway' => 'fake',
        'payment_gateway_reference' => 'checkout_123',
        'payment_checkout_url' => 'https://checkout.example/checkout_123',
    ]);
});

test('checkout success return marks the registration as paid', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->create([
        'email' => 'maria@example.com',
        'payment_status' => 'pending',
        'payment_gateway' => 'asaas',
        'payment_gateway_reference' => 'checkout_123',
    ]);

    $this->get(URL::temporarySignedRoute(
        'registration.payment.success',
        now()->addMinutes(30),
        ['registration' => $registration]
    ))
        ->assertRedirectToRoute('registration')
        ->assertSessionHas('status');

    expect($registration->refresh()->payment_status)->toBe('paid');

    Mail::assertSent(ParticipantRegistrationUpdated::class, function (ParticipantRegistrationUpdated $mail): bool {
        return $mail->hasTo('maria@example.com')
            && $mail->registration->payment_status === 'paid';
    });
});

test('paid registration requires a billing document for checkout', function () {
    $raceModality = RaceModality::factory()->create([
        'price' => 25,
    ]);

    $registration = ParticipantRegistration::factory()->make([
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => $registration->participant_cpf,
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => $registration->guardian_cpf,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'billing_name' => 'Maria Silva',
        'billing_address' => 'Rua das Flores',
        'billing_address_number' => '123',
        'billing_province' => 'Centro',
        'billing_postal_code' => '70000000',
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors('billing_document');
});

test('paid registration requires billing address data for checkout', function () {
    $raceModality = RaceModality::factory()->create([
        'price' => 25,
    ]);

    $registration = ParticipantRegistration::factory()->make([
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => $registration->participant_cpf,
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => $registration->guardian_cpf,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'billing_document' => '52998224725',
        'billing_name' => 'Teste',
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors([
            'billing_name',
            'billing_address',
            'billing_address_number',
            'billing_province',
            'billing_postal_code',
        ]);
});

test('paid registration rejects an invalid billing document before checkout', function () {
    $raceModality = RaceModality::factory()->create([
        'price' => 25,
    ]);

    $registration = ParticipantRegistration::factory()->make([
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => $registration->participant_cpf,
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => $registration->guardian_cpf,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'billing_document' => '37829155412',
        'billing_name' => 'Lucas Gabriel da Silva',
        'billing_address' => 'Rua 20',
        'billing_address_number' => '8',
        'billing_province' => 'Centro',
        'billing_postal_code' => '72900000',
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors('billing_document');
});

test('checkout gateway failure returns the participant to the form with the gateway message', function () {
    Mail::fake();

    PaymentGatewaySetting::query()->create([
        'gateway' => 'asaas',
        'is_enabled' => true,
        'environment' => 'sandbox',
        'api_key' => 'test-key',
        'checkout_minutes_to_expire' => 60,
        'billing_types' => ['PIX', 'CREDIT_CARD'],
        'charge_types' => ['DETACHED'],
    ]);

    $raceModality = RaceModality::factory()->create([
        'price' => 25,
    ]);

    $this->app->bind(PaymentGateway::class, fn (): PaymentGateway => new class implements PaymentGateway
    {
        public function createCheckout(CheckoutRequest $request): CheckoutResponse
        {
            throw new RuntimeException('Gateway indisponivel.');
        }
    });

    $registration = ParticipantRegistration::factory()->make([
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => $registration->participant_cpf,
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => $registration->guardian_cpf,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'billing_document' => '52998224725',
        'billing_name' => 'Maria Silva',
        'billing_address' => 'Rua das Flores',
        'billing_address_number' => '123',
        'billing_province' => 'Centro',
        'billing_postal_code' => '70000000',
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors('checkout');

    Mail::assertNothingSent();
});

test('registration submission validates required fields', function () {
    $this->post(route('registration.store'), [])
        ->assertSessionHasErrors([
            'athlete_name',
            'birth_date',
            'participant_cpf',
            'phone',
            'email',
            'race_modality_id',
        ]);
});

test('registration submission rejects inactive modalities', function () {
    $raceModality = RaceModality::factory()->create([
        'is_active' => false,
    ]);

    $registration = ParticipantRegistration::factory()->make();

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => $registration->participant_cpf,
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => $registration->guardian_cpf,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors('race_modality_id');
});

test('registration submission rejects invalid participant and guardian cpf and phone', function () {
    $raceModality = RaceModality::factory()->create();
    $registration = ParticipantRegistration::factory()->make();

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'participant_cpf' => '111.111.111-11',
        'guardian_name' => 'Joao Silva',
        'guardian_cpf' => '222.222.222-22',
        'phone' => '12345',
        'email' => $registration->email,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors([
            'participant_cpf',
            'guardian_cpf',
            'phone',
        ]);
});

test('registration update email shows cancelled status', function () {
    $registration = ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'payment_status' => 'cancelled',
    ]);

    $mail = new ParticipantRegistrationUpdated($registration);

    $mail->assertSeeInHtml('Maria Silva');
    $mail->assertSeeInHtml('Cancelado');
    $mail->assertSeeInHtml('Esta inscricao foi cancelada');
});

test('an authenticated admin can print the registration list', function () {
    config(['app.env' => 'local']);

    $user = User::factory()->create();

    ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'payment_status' => 'paid',
    ]);

    $this->actingAs($user)
        ->get(ParticipantRegistrationResource::getUrl('print'))
        ->assertSuccessful()
        ->assertSee('Lista de inscricoes')
        ->assertSee('Maria Silva')
        ->assertSee('maria@example.com')
        ->assertSee('Pago');
});
