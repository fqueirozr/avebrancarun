<?php

use App\Filament\Resources\ParticipantRegistrations\Pages\EditParticipantRegistration;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('generates a unique four digit code for a pathfinder', function () {
    $pathfinder = Pathfinder::factory()->create(['code' => null]);

    expect($pathfinder->code)->toMatch('/^\d{4}$/');
});

it('calculates at most three pathfinder kit upgrades', function () {
    $kit = Kit::factory()->make([
        'type' => Kit::TypePathfinder,
        'upgrade_1_referrals' => 2,
        'upgrade_2_referrals' => 4,
        'upgrade_3_referrals' => 6,
    ]);

    expect($kit->upgradeLevelFor(0))->toBe(0)
        ->and($kit->upgradeLevelFor(4))->toBe(2)
        ->and($kit->upgradeLevelFor(20))->toBe(3);
});

it('stores what each pathfinder kit upgrade adds', function () {
    $kit = Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_contents' => 'Camiseta premium',
        'upgrade_2_contents' => 'Boné exclusivo',
        'upgrade_3_contents' => 'Mochila personalizada',
    ]);

    expect($kit->refresh())
        ->upgrade_1_contents->toBe('Camiseta premium')
        ->upgrade_2_contents->toBe('Boné exclusivo')
        ->upgrade_3_contents->toBe('Mochila personalizada');
});

it('shows the kit additions acquired by a pathfinder in the admin registration panel', function () {
    $kit = Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_contents' => 'Camiseta premium',
        'upgrade_2_contents' => 'Boné exclusivo',
        'upgrade_3_contents' => 'Mochila personalizada',
    ]);
    $registration = ParticipantRegistration::factory()->create([
        'kit_id' => $kit->id,
        'pathfinder_upgrade_level' => 2,
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test(EditParticipantRegistration::class, ['record' => $registration->getRouteKey()])
        ->assertOk()
        ->assertSee('Acréscimos do kit adquiridos pelas indicações')
        ->assertSee('Camiseta premium')
        ->assertSee('Boné exclusivo')
        ->assertDontSee('Mochila personalizada');
});

it('relates normal registrations to their referring pathfinder', function () {
    $pathfinder = Pathfinder::factory()->create();
    $registration = ParticipantRegistration::factory()->create(['referred_by_pathfinder_id' => $pathfinder->id]);

    expect($registration->referredByPathfinder->is($pathfinder))->toBeTrue()
        ->and($pathfinder->referrals)->toHaveCount(1);
});

it('uses a pathfinder kit registration only as identification', function () {
    $pathfinder = Pathfinder::factory()->create();

    ParticipantRegistration::factory()->create([
        'pathfinder_id' => $pathfinder->id,
        'referred_by_pathfinder_id' => null,
    ]);

    expect($pathfinder->registration)->not->toBeNull()
        ->and($pathfinder->referrals)->toHaveCount(0);
});
