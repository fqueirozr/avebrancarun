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
        'sex' => 'female',
        'participant_cpf' => '529.982.247-25',
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => '153.509.460-56',
        'phone' => '(11) 99999-9999',
        'email' => $registration->email,
        'billing_document' => $registration->billing_document,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
        'emergency_contact_name' => 'Ana Silva',
        'emergency_contact_phone' => '(11) 98888-7777',
        'health_notes' => 'Alergia a amendoim.',
        'promotional_opt_in' => '1',
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
    ])
        ->assertRedirectToRoute('registration')
        ->assertSessionHas('status');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'sex' => 'female',
        'participant_cpf' => '52998224725',
        'guardian_cpf' => '15350946056',
        'phone' => '11999999999',
        'billing_document' => $registration->billing_document,
        'race_modality_id' => $raceModality->id,
        'modality' => 'Adulto a partir de 16 anos - 6 km',
        'notes' => $registration->notes,
        'emergency_contact_name' => 'Ana Silva',
        'emergency_contact_phone' => '11988887777',
        'promotional_opt_in' => true,
        'privacy_policy_version' => ParticipantRegistration::PrivacyPolicyVersion,
        'payment_status' => 'pending',
    ]);

    $storedRegistration = ParticipantRegistration::query()->where('email', 'maria@example.com')->firstOrFail();

    expect($storedRegistration->health_notes)->toBe('Alergia a amendoim.')
        ->and($storedRegistration->privacy_policy_accepted_at)->not->toBeNull()
        ->and($storedRegistration->privacy_policy_acceptance_ip)->not->toBeNull()
        ->and($storedRegistration->privacy_policy_acceptance_user_agent)->not->toBeNull();

    Mail::assertSent(ParticipantRegistrationReceived::class, function (ParticipantRegistrationReceived $mail) {
        return $mail->hasTo('maria@example.com')
            && $mail->registration->athlete_name === 'Maria Silva'
            && $mail->registration->modality === 'Adulto a partir de 16 anos - 6 km';
    });
});

test('registration submission requires mandatory declarations', function () {
    $raceModality = RaceModality::factory()->create([
        'price' => null,
    ]);

    $registration = ParticipantRegistration::factory()->make([
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'sex' => $registration->sex,
        'participant_cpf' => '529.982.247-25',
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => '153.509.460-56',
        'phone' => '(11) 99999-9999',
        'email' => $registration->email,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors([
            'accepted_regulation',
            'accepted_privacy_policy',
            'accepted_fitness_declaration',
        ]);

    expect(ParticipantRegistration::query()->count())->toBe(0);
});

test('minor participant registration requires guardian data', function () {
    $raceModality = RaceModality::factory()->create([
        'price' => null,
    ]);

    $registration = ParticipantRegistration::factory()->make([
        'birth_date' => now()->subYears(12)->format('Y-m-d'),
        'race_modality_id' => $raceModality->id,
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'sex' => $registration->sex,
        'participant_cpf' => '529.982.247-25',
        'phone' => '(11) 99999-9999',
        'email' => $registration->email,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
    ])
        ->assertSessionHasErrors([
            'guardian_name',
            'guardian_cpf',
        ]);

    expect(ParticipantRegistration::query()->count())->toBe(0);
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
        'sex' => $registration->sex,
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
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
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
        'sex' => $registration->sex,
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
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
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
        'sex' => $registration->sex,
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
        'sex' => $registration->sex,
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
        'sex' => $registration->sex,
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
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
    ])
        ->assertSessionHasErrors('checkout');

    Mail::assertNothingSent();
});

test('registration submission validates required fields', function () {
    $this->post(route('registration.store'), [])
        ->assertSessionHasErrors([
            'athlete_name',
            'birth_date',
            'sex',
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
        'sex' => $registration->sex,
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
        'sex' => $registration->sex,
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
    $mail->assertSeeInHtml('Esta inscrição foi cancelada');
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
        ->assertSee('Lista de inscrições')
        ->assertSee('Maria Silva')
        ->assertSee('maria@example.com')
        ->assertSee('Pago');
});
