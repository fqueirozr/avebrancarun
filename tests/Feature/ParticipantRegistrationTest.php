<?php

use App\Mail\ParticipantRegistrationReceived;
use App\Models\ParticipantRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('a participant can submit a registration', function () {
    Mail::fake();

    $registration = ParticipantRegistration::factory()->make([
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'modality' => 'Adulto a partir de 16 anos - 6 km',
    ]);

    $this->post(route('registration.store'), [
        'athlete_name' => $registration->athlete_name,
        'birth_date' => $registration->birth_date->format('Y-m-d'),
        'guardian_name' => $registration->guardian_name,
        'phone' => $registration->phone,
        'email' => $registration->email,
        'modality' => $registration->modality,
        'notes' => $registration->notes,
    ])
        ->assertRedirectToRoute('registration')
        ->assertSessionHas('status');

    $this->assertDatabaseHas(ParticipantRegistration::class, [
        'athlete_name' => 'Maria Silva',
        'email' => 'maria@example.com',
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
            'modality',
        ]);
});
