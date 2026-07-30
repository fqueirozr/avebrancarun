<?php

use App\Filament\Resources\ParticipantRegistrations\Pages\EditParticipantRegistration;
use App\Models\ParticipantRegistration;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

it('allows an admin to upload a pix receipt', function () {
    Storage::fake('local');

    $this->actingAs(User::factory()->create());

    $registration = ParticipantRegistration::factory()->create([
        'pix_receipt_path' => null,
        'pix_receipt_submitted_at' => null,
    ]);

    Livewire::test(EditParticipantRegistration::class, ['record' => $registration->getRouteKey()])
        ->fillForm([
            'pix_receipt_path' => UploadedFile::fake()->image('comprovante.png'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $registration->refresh();

    expect($registration->pix_receipt_path)
        ->toStartWith('pix-receipts/')
        ->and($registration->pix_receipt_submitted_at)
        ->not->toBeNull();

    Storage::disk('local')->assertExists($registration->pix_receipt_path);
});

it('rejects an invalid pix receipt uploaded by an admin', function () {
    Storage::fake('local');

    $this->actingAs(User::factory()->create());

    $registration = ParticipantRegistration::factory()->create([
        'pix_receipt_path' => null,
    ]);

    Livewire::test(EditParticipantRegistration::class, ['record' => $registration->getRouteKey()])
        ->fillForm([
            'pix_receipt_path' => UploadedFile::fake()->create(
                'comprovante.txt',
                10,
                'text/plain',
            ),
        ])
        ->call('save')
        ->assertHasFormErrors(['pix_receipt_path']);

    expect($registration->refresh()->pix_receipt_path)->toBeNull();
});
