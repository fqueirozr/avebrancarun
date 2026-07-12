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
            'password' => 'Senha-Segura1!',
            'password_confirmation' => 'Senha-Segura1!',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect($user->name)->toBe('Administradora')
        ->and(Hash::check('Senha-Segura1!', $user->password))->toBeTrue();
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
            'password' => 'Senha-Segura1!',
            'password_confirmation' => 'Senha-Diferente1!',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'email' => 'unique',
            'password' => 'confirmed',
        ]);
});

it('exige senha forte para usuários administrativos', function (string $password) {
    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Nova administradora',
            'email' => 'nova-admin@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])
        ->call('create')
        ->assertHasFormErrors(['password']);
})->with([
    'menos de 12 caracteres' => 'Senha1!',
    'sem letra maiúscula' => 'senha-segura1!',
    'sem letra minúscula' => 'SENHA-SEGURA1!',
    'sem número' => 'Senha-Segura!',
    'sem símbolo' => 'SenhaSegura123',
]);
