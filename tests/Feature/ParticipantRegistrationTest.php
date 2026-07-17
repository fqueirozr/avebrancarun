<?php

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Mail\ParticipantRegistrationReceived;
use App\Mail\ParticipantRegistrationUpdated;
use App\Models\EventSetting;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use App\Models\RaceModality;
use App\Models\User;
use App\Payments\CheckoutRequest;
use App\Payments\CheckoutResponse;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

/**
 * @return array<string, mixed>
 */
function validRegistrationPayload(RaceModality $raceModality, Kit $kit, array $overrides = []): array
{
    return array_replace([
        'athlete_name' => 'Maria Silva',
        'shirt_size' => 'M',
        'birth_date' => '1990-05-10',
        'sex' => 'female',
        'participant_cpf' => '529.982.247-25',
        'guardian_name' => 'Ana Silva',
        'guardian_cpf' => '153.509.460-56',
        'phone' => '(11) 99999-9999',
        'email' => 'maria@example.com',
        'billing_document' => '529.982.247-25',
        'billing_name' => 'Maria Silva',
        'billing_address' => 'Rua das Flores',
        'billing_address_number' => '123',
        'billing_province' => 'Centro',
        'billing_postal_code' => '70000000',
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
        'accepted_data_confirmation' => '1',
    ], $overrides);
}

test('billing postal code is normalized before validation', function () {
    Mail::fake();
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'billing_postal_code' => '70.000-000',
    ]))->assertSessionDoesntHaveErrors('billing_postal_code');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'billing_postal_code' => '70000000',
    ]);
});

test('shirt size is required when the selected kit includes a shirt', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0, 'has_shirt' => true]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'shirt_size' => null,
    ]))->assertSessionHasErrors('shirt_size');
});

test('shirt size is discarded when the selected kit does not include a shirt', function () {
    Mail::fake();
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0, 'has_shirt' => false]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'shirt_size' => 'M',
    ]))->assertSessionDoesntHaveErrors('shirt_size');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'kit_id' => $kit->id,
        'shirt_size' => null,
    ]);
});

test('payer data is optional when checkout is not active', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'billing_document' => null,
        'billing_name' => null,
        'billing_address' => null,
        'billing_address_number' => null,
        'billing_province' => null,
        'billing_postal_code' => null,
    ]))->assertSessionDoesntHaveErrors([
        'billing_document',
        'billing_name',
        'billing_address',
        'billing_address_number',
        'billing_province',
        'billing_postal_code',
    ]);
});

test('registration submission rejects an expired deadline', function () {
    EventSetting::factory()->create([
        'registration_deadline' => now()->subMinute(),
    ]);
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit))
        ->assertSessionHasErrors('registration');

    expect(ParticipantRegistration::query()->count())->toBe(0);
});

test('special kit requires acknowledgement of its rules', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create([
        'price' => 0,
        'type' => Kit::TypePcd60,
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit))
        ->assertSessionHasErrors('accepted_special_kit_rules');

    expect(ParticipantRegistration::query()->count())->toBe(0);
});

test('every non-standard kit requires acknowledgement of its rules', function (string $type) {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create([
        'price' => 0,
        'type' => $type,
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit))
        ->assertSessionHasErrors('accepted_special_kit_rules');

    expect(ParticipantRegistration::query()->count())->toBe(0);
})->with([
    'social' => Kit::TypeSocial,
    'pathfinder' => Kit::TypePathfinder,
]);

test('special kit acknowledgement is recorded for audit', function () {
    Mail::fake();
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create([
        'price' => 0,
        'type' => Kit::TypePcd60,
    ]);

    $this->withServerVariables([
        'REMOTE_ADDR' => '203.0.113.10',
        'HTTP_USER_AGENT' => 'Registration audit test',
    ])->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'accepted_special_kit_rules' => '1',
    ]))->assertSessionDoesntHaveErrors();

    $registration = ParticipantRegistration::query()->sole();

    expect($registration->special_kit_rules_accepted_at)->not->toBeNull()
        ->and($registration->special_kit_rules_version)->toBe(ParticipantRegistration::SpecialKitRulesVersion)
        ->and($registration->special_kit_rules_acceptance_ip)->toBe('203.0.113.10')
        ->and($registration->special_kit_rules_acceptance_user_agent)->toBe('Registration audit test');
});

test('regulation acceptance is recorded for audit', function () {
    Mail::fake();
    EventSetting::factory()->create([
        'regulation' => '<p>Regulamento oficial versão 2</p>',
    ]);
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

    $this->withServerVariables([
        'REMOTE_ADDR' => '203.0.113.20',
        'HTTP_USER_AGENT' => 'Regulation audit test',
    ])->post(route('registration.store'), validRegistrationPayload($raceModality, $kit))
        ->assertSessionDoesntHaveErrors();

    $registration = ParticipantRegistration::query()->sole();

    expect($registration->regulation_accepted_at)->not->toBeNull()
        ->and($registration->regulation_version)->toBe(hash('sha256', '<p>Regulamento oficial versão 2</p>'))
        ->and($registration->regulation_acceptance_ip)->toBe('203.0.113.20')
        ->and($registration->regulation_acceptance_user_agent)->toBe('Regulation audit test');
});

test('registration submission rejects a reached event limit', function () {
    EventSetting::factory()->create([
        'max_registrations' => 1,
    ]);
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);
    ParticipantRegistration::factory()->create([
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'participant_cpf' => '153.509.460-56',
    ]))->assertSessionHasErrors('registration');
});

test('registration submission rejects a reached modality limit', function () {
    $raceModality = RaceModality::factory()->create([
        'max_participants' => 1,
    ]);
    $kit = Kit::factory()->create(['price' => 0]);
    ParticipantRegistration::factory()->create([
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'participant_cpf' => '153.509.460-56',
    ]))->assertSessionHasErrors('race_modality_id');
});

test('registration submission rejects a reached kit quantity limit', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create([
        'price' => 0,
        'max_quantity' => 1,
    ]);
    ParticipantRegistration::factory()->create([
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'participant_cpf' => '153.509.460-56',
    ]))->assertSessionHasErrors('kit_id');

    expect(ParticipantRegistration::query()->count())->toBe(1);
});

test('cancelled registrations do not consume the kit quantity limit', function () {
    Mail::fake();

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create([
        'price' => 0,
        'max_quantity' => 1,
    ]);
    ParticipantRegistration::factory()->create([
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
        'payment_status' => 'cancelled',
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'participant_cpf' => '153.509.460-56',
    ]))->assertSessionDoesntHaveErrors('kit_id');

    expect(ParticipantRegistration::query()->count())->toBe(2);
});

test('registration submission calculates the age range on the race date', function (string $birthDate, bool $isAccepted) {
    Mail::fake();

    $raceModality = RaceModality::factory()->create([
        'age_start' => 16,
        'age_end' => 17,
        'race_date' => '2026-09-20',
    ]);
    $kit = Kit::factory()->create(['price' => 0]);

    $response = $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'birth_date' => $birthDate,
    ]));

    if ($isAccepted) {
        $response->assertSessionDoesntHaveErrors('birth_date');
        expect(ParticipantRegistration::query()->count())->toBe(1);
    } else {
        $response->assertSessionHasErrors('birth_date');
        expect(ParticipantRegistration::query()->count())->toBe(0);
    }
})->with([
    'turns 16 on the race date' => ['2010-09-20', true],
    'is still 15 on the race date' => ['2010-09-21', false],
    'turns 18 on the race date' => ['2008-09-20', false],
]);

test('registration submission uses the configured event date when the modality has no race date', function (string $birthDate, bool $isAccepted) {
    Mail::fake();

    EventSetting::factory()->create(['event_date' => '20/09/2026']);
    $raceModality = RaceModality::factory()->create([
        'age_start' => 16,
        'age_end' => 17,
        'race_date' => null,
    ]);
    $kit = Kit::factory()->create(['price' => 0]);

    $response = $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'birth_date' => $birthDate,
    ]));

    if ($isAccepted) {
        $response->assertSessionDoesntHaveErrors('birth_date');
    } else {
        $response->assertSessionHasErrors('birth_date');
    }
})->with([
    'turns 16 on the event date' => ['2010-09-20', true],
    'is still 15 on the event date' => ['2010-09-21', false],
]);

test('an athlete cannot register twice with the same cpf', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);
    ParticipantRegistration::factory()->create([
        'participant_cpf' => '52998224725',
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
    ]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit))
        ->assertSessionHasErrors('participant_cpf');

    expect(ParticipantRegistration::query()->count())->toBe(1);
});

test('a participant can submit a registration', function () {
    Mail::fake();

    $raceModality = RaceModality::factory()->create([
        'name' => 'Adulto a partir de 16 anos',
        'type' => 'Adulto',
        'age_start' => 16,
        'distance' => '6 km',
    ]);
    $kit = Kit::factory()->create(['price' => 0]);

    $registration = ParticipantRegistration::factory()->make([
        'athlete_name' => 'Maria Silva',
        'birth_date' => '1990-05-10',
        'email' => 'maria@example.com',
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
        'modality' => $raceModality->displayName(),
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'shirt_size' => 'M',
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'sex' => 'female',
        'participant_cpf' => '529.982.247-25',
        'guardian_name' => $registration->guardian_name,
        'guardian_cpf' => '153.509.460-56',
        'phone' => '(11) 99999-9999',
        'email' => $registration->email,
        'billing_document' => $registration->billing_document,
        'billing_name' => 'Maria Silva',
        'billing_address' => 'Rua das Flores',
        'billing_address_number' => '123',
        'billing_province' => 'Centro',
        'billing_postal_code' => '70000000',
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
        'emergency_contact_name' => 'Ana Silva',
        'emergency_contact_phone' => '(11) 98888-7777',
        'health_notes' => 'Alergia a amendoim.',
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
        'accepted_data_confirmation' => '1',
    ])
        ->assertRedirectToRoute('registration')
        ->assertSessionHas('status');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'athlete_name' => 'Maria Silva',
        'shirt_size' => 'M',
        'email' => 'maria@example.com',
        'sex' => 'female',
        'participant_cpf' => '52998224725',
        'guardian_cpf' => '15350946056',
        'phone' => '11999999999',
        'billing_document' => $registration->billing_document,
        'race_modality_id' => $raceModality->id,
        'kit_id' => $kit->id,
        'modality' => 'Adulto a partir de 16 anos - 6 km',
        'result_category' => 'Feminino 30–39',
        'notes' => $registration->notes,
        'emergency_contact_name' => 'Ana Silva',
        'emergency_contact_phone' => '11988887777',
        'privacy_policy_version' => ParticipantRegistration::PrivacyPolicyVersion,
        'payment_status' => 'pending',
    ]);

    $storedRegistration = ParticipantRegistration::query()->where('email', 'maria@example.com')->firstOrFail();

    expect($storedRegistration->health_notes)->toBe('Alergia a amendoim.')
        ->and($storedRegistration->privacy_policy_accepted_at)->not->toBeNull()
        ->and($storedRegistration->privacy_policy_acceptance_ip)->not->toBeNull()
        ->and($storedRegistration->privacy_policy_acceptance_user_agent)->not->toBeNull()
        ->and($storedRegistration->data_confirmation_accepted_at)->not->toBeNull()
        ->and($storedRegistration->data_confirmation_acceptance_ip)->not->toBeNull()
        ->and($storedRegistration->data_confirmation_acceptance_user_agent)->not->toBeNull();

    Mail::assertSent(ParticipantRegistrationReceived::class, function (ParticipantRegistrationReceived $mail) {
        return $mail->hasTo('maria@example.com')
            && $mail->registration->athlete_name === 'Maria Silva'
            && $mail->registration->modality === 'Adulto a partir de 16 anos - 6 km';
    });
});

test('registration submission requires mandatory declarations', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

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
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors([
            'accepted_regulation',
            'accepted_privacy_policy',
            'accepted_fitness_declaration',
            'accepted_data_confirmation',
        ]);

    expect(ParticipantRegistration::query()->count())->toBe(0);
});

test('minor participant registration requires guardian data', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

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
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
        'accepted_data_confirmation' => '1',
    ])
        ->assertSessionHasErrors([
            'guardian_name',
            'guardian_cpf',
        ]);

    expect(ParticipantRegistration::query()->count())->toBe(0);
});

test('legal representative option requires and stores representative data', function () {
    Mail::fake();

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'guardian_name' => null,
        'guardian_cpf' => null,
        'filled_by_legal_representative' => '1',
    ]))->assertSessionHasErrors(['guardian_name', 'guardian_cpf']);

    $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit, [
        'filled_by_legal_representative' => '1',
    ]))->assertRedirectToRoute('registration');

    $registration = ParticipantRegistration::query()->sole();

    expect($registration->filled_by_legal_representative)->toBeTrue()
        ->and($registration->guardian_name)->toBe('Ana Silva')
        ->and($registration->guardian_cpf)->toBe('15350946056');
});

test('every registration receives a unique protocol shown in its receipt', function () {
    $firstRegistration = ParticipantRegistration::factory()->create([
        'protocol_number' => 'PROTOCOLO-INFORMADO-EXTERNAMENTE',
    ]);
    $secondRegistration = ParticipantRegistration::factory()->create();

    expect($firstRegistration->protocol_number)
        ->toStartWith('AVR-')
        ->toMatch('/^AVR-\d{6}$/')
        ->not->toBe('PROTOCOLO-INFORMADO-EXTERNAMENTE')
        ->not->toBe($secondRegistration->protocol_number);

    (new ParticipantRegistrationReceived($firstRegistration))
        ->assertSeeInHtml($firstRegistration->protocol_number);
});

test('the registration factory generates unique protocols without model events', function () {
    $registrations = ParticipantRegistration::withoutEvents(
        fn () => ParticipantRegistration::factory()->count(10)->create()
    );

    $protocolNumbers = $registrations->pluck('protocol_number');

    expect($protocolNumbers)->each->toStartWith('AVR-');
    expect($protocolNumbers)->each->toMatch('/^AVR-\d{6}$/');
    expect($protocolNumbers->unique())->toHaveCount(10);
});

test('registrations receive unique random four digit bib numbers', function () {
    $registrations = ParticipantRegistration::factory()->count(25)->create();
    $bibNumbers = $registrations->pluck('bib_number');

    expect($bibNumbers)->each->toMatch('/^\d{4}$/');
    expect($bibNumbers->unique())->toHaveCount(25);
});

test('a paid registration uses the manual pix screen when configured', function () {
    Mail::fake();

    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
        'pix_receiver_name' => 'Ave Branca Run',
        'pix_receiver_city' => 'Sao Paulo',
        'pix_bank' => 'Banco do Brasil',
        'pix_agency' => '1234-5',
        'pix_account' => '98765-4',
        'pix_account_holder' => 'Associação Ave Branca',
    ]);

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

    $response = $this->post(route('registration.store'), validRegistrationPayload($raceModality, $kit));
    $registration = ParticipantRegistration::query()->sole();

    $response->assertRedirectContains("/inscricao/{$registration->id}/pix");
    expect($registration->payment_status)->toBe('pending');

    $this->get($response->headers->get('Location'))
        ->assertSuccessful()
        ->assertSee('financeiro@example.com')
        ->assertSee('R$ 25,00')
        ->assertSee('Pix copia e cola')
        ->assertSee('Banco do Brasil')
        ->assertSee('1234-5')
        ->assertSee('98765-4')
        ->assertSee('Associação Ave Branca')
        ->assertSee('5921ASSOCIACAO AVE BRANCA')
        ->assertSee('confirme no aplicativo do seu banco')
        ->assertSee('Confira também o nome e o CPF do pagador')
        ->assertSee($registration->protocol_number);
});

test('a pix receipt is stored privately and puts the registration under review', function () {
    Storage::fake('local');

    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);

    $registration = ParticipantRegistration::withoutEvents(fn () => ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'bib_number' => null,
    ]));
    $url = URL::temporarySignedRoute('registration.pix.store', now()->addHour(), ['registration' => $registration]);

    $response = $this->post($url, [
        'billing_name' => 'Maria Silva',
        'billing_document' => '529.982.247-25',
        'pix_receipt' => UploadedFile::fake()->image('comprovante.png'),
        'payer_data_confirmed' => '1',
    ])->assertSessionHas('status');

    $registration->refresh();

    $response->assertRedirectContains("/atleta/{$registration->id}");
    $this->get($response->headers->get('Location'))->assertSuccessful();

    expect($registration->payment_status)->toBe('under_review')
        ->and($registration->bib_number)->toMatch('/^\d{4}$/')
        ->and($registration->billing_name)->toBe('Maria Silva')
        ->and($registration->billing_document)->toBe('52998224725')
        ->and($registration->pix_receipt_submitted_at)->not->toBeNull();
    Storage::disk('local')->assertExists($registration->pix_receipt_path);
});

test('payer name and cpf are required when submitting a pix receipt', function () {
    Storage::fake('local');

    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);

    $registration = ParticipantRegistration::factory()->create(['payment_status' => 'pending']);
    $url = URL::temporarySignedRoute('registration.pix.store', now()->addHour(), ['registration' => $registration]);

    $this->post($url, [
        'billing_name' => '',
        'billing_document' => '111.111.111-11',
        'pix_receipt' => UploadedFile::fake()->image('comprovante.png'),
    ])->assertSessionHasErrors(['billing_name', 'billing_document']);

    expect($registration->fresh()->payment_status)->toBe('pending');
});

test('payer data confirmation is required when submitting a pix receipt', function () {
    Storage::fake('local');

    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);

    $registration = ParticipantRegistration::factory()->create(['payment_status' => 'pending']);
    $url = URL::temporarySignedRoute('registration.pix.store', now()->addHour(), ['registration' => $registration]);

    $this->post($url, [
        'billing_name' => 'Maria Silva',
        'billing_document' => '529.982.247-25',
        'pix_receipt' => UploadedFile::fake()->image('comprovante.png'),
    ])->assertSessionHasErrors('payer_data_confirmed');

    expect($registration->fresh()->payment_status)->toBe('pending');
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

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

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
        'shirt_size' => 'M',
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
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
        'accepted_data_confirmation' => '1',
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
    PaymentGatewaySetting::factory()->create(['is_enabled' => true, 'api_key' => 'test-key']);

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

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
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
        'accepted_data_confirmation' => '1',
    ])
        ->assertSessionHasErrors('billing_document');
});

test('paid registration requires billing address data for checkout', function () {
    PaymentGatewaySetting::factory()->create(['is_enabled' => true, 'api_key' => 'test-key']);

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

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
        'kit_id' => $kit->id,
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
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

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
        'kit_id' => $kit->id,
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

    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 25]);

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
        'shirt_size' => 'M',
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
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
        'accepted_regulation' => '1',
        'accepted_privacy_policy' => '1',
        'accepted_fitness_declaration' => '1',
        'accepted_data_confirmation' => '1',
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
            'kit_id',
        ]);
});

test('registration submission rejects inactive modalities', function () {
    $raceModality = RaceModality::factory()->create([
        'is_active' => false,
    ]);
    $kit = Kit::factory()->create(['price' => 0]);

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
        'kit_id' => $kit->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors('race_modality_id');
});

test('registration submission rejects invalid participant and guardian cpf and phone', function () {
    $raceModality = RaceModality::factory()->create();
    $kit = Kit::factory()->create(['price' => 0]);
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
        'kit_id' => $kit->id,
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

test('an authenticated admin can print the paid kit delivery list with a signature field', function () {
    config(['app.env' => 'local']);

    $user = User::factory()->create();

    $kit = Kit::factory()->create([
        'name' => 'Kit Desbravador',
        'type' => Kit::TypePathfinder,
        'upgrade_1_contents' => 'Camiseta exclusiva',
        'upgrade_2_contents' => 'Boné do evento',
        'upgrade_3_contents' => 'Mochila premium',
    ]);

    ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
        'shirt_size' => 'GG',
        'email' => 'maria@example.com',
        'payment_status' => 'paid',
        'kit_id' => $kit->id,
        'pathfinder_upgrade_level' => 2,
    ]);

    ParticipantRegistration::factory()->create([
        'athlete_name' => 'João Pendente',
        'payment_status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(ParticipantRegistrationResource::getUrl('print'))
        ->assertSuccessful()
        ->assertSee('Lista de entrega de kits')
        ->assertSee('Maria Silva')
        ->assertSee('Kit Desbravador')
        ->assertSee('GG')
        ->assertSee('Upgrade do Desbravador')
        ->assertSee('Nível 2')
        ->assertSee('Camiseta exclusiva')
        ->assertSee('Boné do evento')
        ->assertDontSee('Mochila premium')
        ->assertSee('Assinatura do recebedor')
        ->assertDontSee('maria@example.com')
        ->assertDontSee('João Pendente');
});
