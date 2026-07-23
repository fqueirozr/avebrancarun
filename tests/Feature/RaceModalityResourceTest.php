<?php

use App\Filament\Resources\RaceModalities\Pages\CreateRaceModality;
use App\Filament\Resources\RaceModalities\Pages\ListRaceModalities;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('lists races with their operational information', function () {
    $this->actingAs(User::factory()->create());
    $race = RaceModality::factory()->create(['max_participants' => 100]);
    ParticipantRegistration::factory()->for($race)->create();

    Livewire::test(ListRaceModalities::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$race])
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('type')
        ->assertTableColumnExists('race_date')
        ->assertTableColumnExists('participant_registrations_count')
        ->assertTableColumnExists('google_maps_embed_url')
        ->assertTableColumnExists('is_active');
});

it('creates a race using the modernized form', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateRaceModality::class)
        ->fillForm([
            'name' => 'Corrida Juvenil',
            'type' => 'Juvenil',
            'age_start' => 12,
            'age_end' => 15,
            'distance' => '3 km',
            'race_date' => '2026-10-12',
            'race_time' => '08:00',
            'max_participants' => 80,
            'sort_order' => 20,
            'is_active' => true,
            'description' => 'Percurso preparado para atletas juvenis.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(RaceModality::query()->where('name', 'Corrida Juvenil')->first())
        ->not->toBeNull()
        ->age_start->toBe(12)
        ->age_end->toBe(15)
        ->max_participants->toBe(80);
});
