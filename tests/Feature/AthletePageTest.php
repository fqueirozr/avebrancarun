<?php

use App\Models\EventSetting;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
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

test('athlete with pending manual pix payment can access receipt submission', function () {
    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);
    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'payment_checkout_url' => null,
    ]);

    $this->get(URL::signedRoute('athlete.show', ['registration' => $registration]))
        ->assertSuccessful()
        ->assertSeeText('Pagar e enviar comprovante')
        ->assertSee("/inscricao/{$registration->id}/pix", false);
});

test('athlete with pending gateway payment can access checkout', function () {
    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => 'pending',
        'payment_gateway' => 'asaas',
        'payment_checkout_url' => 'https://checkout.example/payment_123',
    ]);

    $this->get(URL::signedRoute('athlete.show', ['registration' => $registration]))
        ->assertSuccessful()
        ->assertSeeText('Ir para o checkout')
        ->assertSee('https://checkout.example/payment_123', false);
});

test('athlete without pending payment does not see a payment action', function (string $paymentStatus) {
    $registration = ParticipantRegistration::factory()->create([
        'payment_status' => $paymentStatus,
        'payment_checkout_url' => 'https://checkout.example/payment_123',
    ]);

    $this->get(URL::signedRoute('athlete.show', ['registration' => $registration]))
        ->assertSuccessful()
        ->assertDontSeeText('Pagar e enviar comprovante')
        ->assertDontSeeText('Ir para o checkout')
        ->assertDontSee('https://checkout.example/payment_123', false);
})->with(['paid', 'under_review', 'cancelled']);
