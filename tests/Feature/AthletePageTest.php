<?php

use App\Models\EventSetting;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('athlete page requires a valid signed link', function () {
    $registration = ParticipantRegistration::factory()->create();

    $this->get(route('athlete.show', $registration))->assertForbidden();
    $this->get(URL::signedRoute('athlete.show', ['registration' => $registration]))->assertSuccessful();
});

test('athlete can see registration and pending race details without sensitive data', function () {
    EventSetting::factory()->create([
        'event_location' => 'Parque da Cidade',
    ]);
    $raceModality = RaceModality::factory()->create([
        'name' => 'Corrida adulta',
        'distance' => '6 km',
        'race_date' => '2026-09-20',
        'race_time' => '07:00',
    ]);
    $registration = ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
        'race_modality_id' => $raceModality,
        'modality' => 'Corrida adulta - 6 km',
        'bib_number' => '421',
        'participant_cpf' => '52998224725',
        'billing_document' => '52998224725',
        'health_notes' => 'Informação médica privada',
        'payment_gateway_reference' => 'pay_secret_123',
    ]);

    $this->get(URL::signedRoute('athlete.show', ['registration' => $registration]))
        ->assertSuccessful()
        ->assertSeeText('Maria Silva')
        ->assertSeeText($registration->protocol_number)
        ->assertSeeText('Corrida adulta - 6 km')
        ->assertSeeText('Parque da Cidade')
        ->assertSeeText('421')
        ->assertSeeText('Aguardando resultado')
        ->assertDontSee('52998224725')
        ->assertDontSee('Informação médica privada')
        ->assertDontSee('pay_secret_123');
});

test('athlete can see official time and rankings after finishing', function () {
    $registration = ParticipantRegistration::factory()->create([
        'result_status' => 'finished',
        'elapsed_time' => '00:42:18',
        'result_category' => 'Masculino 30–39',
        'overall_rank' => 12,
        'sex_rank' => 9,
        'category_rank' => 3,
    ]);

    $this->get(URL::signedRoute('athlete.show', ['registration' => $registration]))
        ->assertSuccessful()
        ->assertSeeText('Concluiu a prova')
        ->assertSeeText('00:42:18')
        ->assertSeeText('Masculino 30–39')
        ->assertSeeText('12º')
        ->assertSeeText('9º')
        ->assertSeeText('3º');
});
