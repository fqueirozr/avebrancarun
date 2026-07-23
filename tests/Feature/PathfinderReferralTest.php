<?php

use App\Filament\Resources\ParticipantRegistrations\Pages\EditParticipantRegistration;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('stores a unique cpf for a pathfinder', function () {
    $pathfinder = Pathfinder::factory()->create(['cpf' => '15350946056']);

    expect($pathfinder->cpf)->toBe('15350946056');
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

it('does not return kit additions before the first upgrade', function () {
    $kit = Kit::factory()->make([
        'type' => Kit::TypePathfinder,
        'upgrade_1_contents' => 'Camiseta premium',
    ]);

    expect($kit->upgradeContentsThroughLevel(0))->toBe([]);
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

it('shows the current level and upgrades before the pathfinder registers for the kit', function () {
    Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_referrals' => 1,
        'upgrade_1_contents' => 'Camiseta premium',
        'upgrade_2_referrals' => 3,
        'upgrade_2_contents' => 'Boné exclusivo',
    ]);
    $pathfinder = Pathfinder::factory()->create();

    ParticipantRegistration::factory()->count(3)->create([
        'pathfinder_id' => null,
        'referred_by_pathfinder_id' => $pathfinder->id,
    ]);

    expect($pathfinder->upgradeLevel())->toBe(2)
        ->and($pathfinder->upgradeContents())->toBe(['Camiseta premium', 'Boné exclusivo']);
});

it('recalculates upgrades when a referral registration is updated or deleted', function () {
    $kit = Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_referrals' => 1,
        'upgrade_2_referrals' => 2,
        'upgrade_3_referrals' => 3,
    ]);
    $firstPathfinder = Pathfinder::factory()->create();
    $secondPathfinder = Pathfinder::factory()->create();
    $firstPathfinderRegistration = ParticipantRegistration::factory()->create([
        'kit_id' => $kit->id,
        'pathfinder_id' => $firstPathfinder->id,
        'referred_by_pathfinder_id' => null,
    ]);
    $secondPathfinderRegistration = ParticipantRegistration::factory()->create([
        'kit_id' => $kit->id,
        'pathfinder_id' => $secondPathfinder->id,
        'referred_by_pathfinder_id' => null,
    ]);
    $referral = ParticipantRegistration::factory()->create([
        'pathfinder_id' => null,
        'referred_by_pathfinder_id' => $firstPathfinder->id,
    ]);

    expect($firstPathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(1)
        ->and($secondPathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(0);

    $referral->update(['referred_by_pathfinder_id' => $secondPathfinder->id]);

    expect($firstPathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(0)
        ->and($secondPathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(1);

    $referral->delete();

    expect($secondPathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(0);
});

it('recalculates existing referrals when the pathfinder registration is linked later', function () {
    $kit = Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_referrals' => 1,
        'upgrade_2_referrals' => 2,
    ]);
    $pathfinder = Pathfinder::factory()->create();

    ParticipantRegistration::factory()->count(2)->create([
        'pathfinder_id' => null,
        'referred_by_pathfinder_id' => $pathfinder->id,
    ]);

    $pathfinderRegistration = ParticipantRegistration::factory()->create([
        'kit_id' => $kit->id,
        'pathfinder_id' => $pathfinder->id,
        'referred_by_pathfinder_id' => null,
    ]);

    expect($pathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(2);
});

it('removes an indication when an updated registration no longer uses a normal kit', function () {
    $pathfinderKit = Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_referrals' => 1,
    ]);
    $socialKit = Kit::factory()->create(['type' => Kit::TypeSocial]);
    $pathfinder = Pathfinder::factory()->create();
    $pathfinderRegistration = ParticipantRegistration::factory()->create([
        'kit_id' => $pathfinderKit->id,
        'pathfinder_id' => $pathfinder->id,
        'referred_by_pathfinder_id' => null,
    ]);
    $referral = ParticipantRegistration::factory()->create([
        'pathfinder_id' => null,
        'referred_by_pathfinder_id' => $pathfinder->id,
    ]);

    expect($pathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(1);

    $referral->update(['kit_id' => $socialKit->id]);

    expect($referral->refresh()->referred_by_pathfinder_id)->toBeNull()
        ->and($pathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(0);
});

it('recalculates the upgrade from the database quantity when kit thresholds are updated', function () {
    $kit = Kit::factory()->create([
        'type' => Kit::TypePathfinder,
        'upgrade_1_referrals' => 2,
        'upgrade_2_referrals' => 4,
        'upgrade_3_referrals' => 6,
    ]);
    $pathfinder = Pathfinder::factory()->create();
    $pathfinderRegistration = ParticipantRegistration::factory()->create([
        'kit_id' => $kit->id,
        'pathfinder_id' => $pathfinder->id,
        'referred_by_pathfinder_id' => null,
    ]);
    ParticipantRegistration::factory()->create([
        'pathfinder_id' => null,
        'referred_by_pathfinder_id' => $pathfinder->id,
    ]);

    expect($pathfinder->referrals()->count())->toBe(1)
        ->and($pathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(0);

    $kit->update(['upgrade_1_referrals' => 1]);

    expect($pathfinderRegistration->refresh()->pathfinder_upgrade_level)->toBe(1);
});
