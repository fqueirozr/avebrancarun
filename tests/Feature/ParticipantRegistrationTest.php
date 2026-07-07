<?php

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Mail\ParticipantRegistrationReceived;
use App\Mail\ParticipantRegistrationUpdated;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('a participant can submit a registration', function () {
    Mail::fake();

    $raceModality = RaceModality::factory()->create([
        'name' => 'Adulto a partir de 16 anos',
        'type' => 'Adulto',
        'age_range' => 'A partir de 16 anos',
        'distance' => '6 km',
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
        'guardian_name' => $registration->guardian_name,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertRedirectToRoute('registration')
        ->assertSessionHas('status');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
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

test('registration submission validates required fields', function () {
    $this->post(route('registration.store'), [])
        ->assertSessionHasErrors([
            'athlete_name',
            'birth_date',
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
        'guardian_name' => $registration->guardian_name,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'race_modality_id' => $raceModality->id,
        'notes' => $registration->notes,
    ])
        ->assertSessionHasErrors('race_modality_id');
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
