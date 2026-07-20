<?php

use App\Filament\Resources\MailSettings\Pages\CreateMailSetting;
use App\Filament\Resources\MailSettings\Pages\EditMailSetting;
use App\Models\MailSetting;
use App\Models\User;
use App\Settings\ApplyMailSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('salva a configuração SMTP com a senha criptografada', function () {
    Livewire::test(CreateMailSetting::class)
        ->fillForm([
            'mailer' => 'smtp',
            'scheme' => 'smtps',
            'host' => 'smtp.example.com',
            'port' => 465,
            'username' => 'mailer-user',
            'password' => 'smtp-secret',
            'from_address' => 'eventos@example.com',
            'from_name' => 'Eventos RunApp',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $settings = MailSetting::query()->sole();
    $storedPassword = DB::table('mail_settings')->value('password');

    expect($settings->password)->toBe('smtp-secret')
        ->and($storedPassword)->not->toBe('smtp-secret')
        ->and($settings->toArray())->not->toHaveKey('password');
});

it('mantém a senha atual quando ela não é informada na edição', function () {
    $settings = MailSetting::factory()->create([
        'password' => 'current-secret',
    ]);

    Livewire::test(EditMailSetting::class, ['record' => $settings->getRouteKey()])
        ->fillForm([
            'host' => 'new-smtp.example.com',
            'password' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($settings->refresh()->host)->toBe('new-smtp.example.com')
        ->and($settings->password)->toBe('current-secret');
});

it('valida a porta e o endereço do remetente', function () {
    Livewire::test(CreateMailSetting::class)
        ->fillForm([
            'mailer' => 'smtp',
            'host' => 'smtp.example.com',
            'port' => 70000,
            'password' => 'smtp-secret',
            'from_address' => 'endereço-inválido',
            'from_name' => 'RunApp',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'port' => 'max',
            'from_address' => 'email',
        ]);
});

it('aplica a configuração persistida ao Laravel', function () {
    $settings = MailSetting::factory()->create([
        'mailer' => 'smtp',
        'scheme' => 'smtps',
        'host' => 'smtp.example.com',
        'port' => 465,
        'username' => 'mailer-user',
        'password' => 'smtp-secret',
        'from_address' => 'eventos@example.com',
        'from_name' => 'Eventos RunApp',
    ]);

    app(ApplyMailSettings::class)->handle($settings);

    expect(config('mail.default'))->toBe('smtp')
        ->and(config('mail.mailers.smtp.scheme'))->toBe('smtps')
        ->and(config('mail.mailers.smtp.host'))->toBe('smtp.example.com')
        ->and(config('mail.mailers.smtp.port'))->toBe(465)
        ->and(config('mail.mailers.smtp.username'))->toBe('mailer-user')
        ->and(config('mail.mailers.smtp.password'))->toBe('smtp-secret')
        ->and(config('mail.from.address'))->toBe('eventos@example.com')
        ->and(config('mail.from.name'))->toBe('Eventos RunApp');
});
