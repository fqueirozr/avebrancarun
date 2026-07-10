<?php

use App\Actions\RecalculateRaceRankings;
use App\Filament\Resources\RaceResults\Pages\ListRaceResults;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('lists all participants without exposing contact data', function () {
    $this->actingAs(User::factory()->create());
    $registrations = ParticipantRegistration::factory()->count(3)->create();

    Livewire::test(ListRaceResults::class)
        ->assertOk()
        ->assertCanSeeTableRecords($registrations)
        ->assertTableColumnExists('athlete_name')
        ->assertTableColumnDoesNotExist('participant_cpf')
        ->assertTableColumnDoesNotExist('email');
});

it('calculates rankings by the shortest time within each race', function () {
    $race = RaceModality::factory()->create();
    $fastest = ParticipantRegistration::factory()->for($race)->create([
        'elapsed_time' => '00:40:00',
        'result_status' => 'finished',
        'sex' => 'female',
        'result_category' => 'Adulto',
    ]);
    $second = ParticipantRegistration::factory()->for($race)->create([
        'elapsed_time' => '00:45:00',
        'result_status' => 'finished',
        'sex' => 'male',
        'result_category' => 'Adulto',
    ]);
    $awaiting = ParticipantRegistration::factory()->for($race)->create([
        'elapsed_time' => null,
        'result_status' => 'awaiting',
    ]);

    app(RecalculateRaceRankings::class)->handle($fastest);

    expect($fastest->refresh())
        ->overall_rank->toBe(1)
        ->sex_rank->toBe(1)
        ->category_rank->toBe(1)
        ->and($second->refresh())
        ->overall_rank->toBe(2)
        ->sex_rank->toBe(1)
        ->category_rank->toBe(2)
        ->and($awaiting->refresh()->overall_rank)->toBeNull();
});

it('calculates rankings for legacy registrations without a linked race modality', function () {
    $fastest = ParticipantRegistration::factory()->create([
        'race_modality_id' => null,
        'modality' => 'Corrida 5 km',
        'elapsed_time' => '00:25:00',
        'result_status' => 'finished',
    ]);
    $second = ParticipantRegistration::factory()->create([
        'race_modality_id' => null,
        'modality' => 'Corrida 5 km',
        'elapsed_time' => '00:30:00',
        'result_status' => 'finished',
    ]);

    app(RecalculateRaceRankings::class)->handle($second);

    expect($fastest->refresh()->overall_rank)->toBe(1)
        ->and($second->refresh()->overall_rank)->toBe(2);
});
