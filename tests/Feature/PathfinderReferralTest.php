<?php

use App\Filament\Resources\Pathfinders\Pages\CreatePathfinder;
use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('stores a unique cpf for a pathfinder', function () {
    $pathfinder = Pathfinder::factory()->create(['cpf' => '15350946056']);

    expect($pathfinder->cpf)->toBe('15350946056');
});

it('preserves a cpf beginning with zero when registering a pathfinder', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreatePathfinder::class)
        ->assertFormFieldExists(
            'cpf',
            fn (TextInput $field): bool => $field->getType() === 'text'
                && $field->getMask() === '999.999.999-99',
        )
        ->fillForm([
            'name' => 'Maria Silva',
            'cpf' => '012.345.678-90',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Pathfinder::query()->where('name', 'Maria Silva')->value('cpf'))
        ->toBe('01234567890');
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
