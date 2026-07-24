<?php

use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('stores a unique cpf for a pathfinder', function () {
    $pathfinder = Pathfinder::factory()->create(['cpf' => '15350946056']);

    expect($pathfinder->cpf)->toBe('15350946056');
});

it('links one pathfinder registration without exposing removed referral fields', function () {
    $pathfinder = Pathfinder::factory()->create();
    $registration = ParticipantRegistration::factory()->create([
        'pathfinder_id' => $pathfinder->id,
    ]);

    expect($pathfinder->registration->is($registration))->toBeTrue()
        ->and($registration->pathfinder->is($pathfinder))->toBeTrue()
        ->and(Schema::hasColumn('participant_registrations', 'referred_by_pathfinder_id'))->toBeFalse()
        ->and(Schema::hasColumn('participant_registrations', 'pathfinder_upgrade_level'))->toBeFalse()
        ->and(Schema::hasColumn('kits', 'upgrade_1_referrals'))->toBeFalse();
});
