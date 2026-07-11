<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lista usuários administrativos', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(ListUsers::class)
        ->assertOk()
        ->assertCanSeeTableRecords($users);
});

it('cria um usuário administrativo com senha protegida', function () {
    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Administradora',
            'email' => 'admin@example.com',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect($user->name)->toBe('Administradora')
        ->and(Hash::check('senha-segura', $user->password))->toBeTrue();
});

it('mantém a senha atual quando ela não é informada na edição', function () {
    $user = User::factory()->create();
    $currentPassword = $user->password;

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm([
            'name' => 'Nome atualizado',
            'password' => null,
            'password_confirmation' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($user->refresh()->name)->toBe('Nome atualizado')
        ->and($user->password)->toBe($currentPassword);
});

it('exige confirmação da senha e e-mail único', function () {
    $existingUser = User::factory()->create();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Outro administrador',
            'email' => $existingUser->email,
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-diferente',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'email' => 'unique',
            'password' => 'confirmed',
        ]);
});
