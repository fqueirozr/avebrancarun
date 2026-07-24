<?php

use App\Filament\Resources\ParticipantRegistrations\Pages\EditParticipantRegistration;
use App\Models\ParticipantRegistration;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows a complete CPF beginning with zero as a text field', function () {
    $this->actingAs(User::factory()->create());

    $registration = ParticipantRegistration::factory()->create([
        'participant_cpf' => '01234567890',
    ]);

    Livewire::test(EditParticipantRegistration::class, ['record' => $registration->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'participant_cpf' => '01234567890',
        ])
        ->assertFormFieldExists(
            'participant_cpf',
            fn (TextInput $field): bool => $field->getType() === 'text'
                && $field->getMask() === '999.999.999-99',
        );
});

it('preserves a CPF beginning with zero when editing a registration', function () {
    $this->actingAs(User::factory()->create());

    $registration = ParticipantRegistration::factory()->create([
        'participant_cpf' => '01234567890',
    ]);

    Livewire::test(EditParticipantRegistration::class, ['record' => $registration->getRouteKey()])
        ->fillForm(['participant_cpf' => '098.765.432-10'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($registration->refresh()->participant_cpf)->toBe('09876543210');
});
