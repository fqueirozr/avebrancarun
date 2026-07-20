<?php

namespace App\Filament\Resources\MailSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MailSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Envio de e-mails')
                    ->description('Use “Log” em desenvolvimento ou configure um servidor SMTP para realizar envios.')
                    ->schema([
                        Select::make('mailer')
                            ->label('Método de envio')
                            ->options([
                                'log' => 'Log (não envia)',
                                'smtp' => 'SMTP',
                            ])
                            ->default('log')
                            ->live()
                            ->required(),
                        Select::make('scheme')
                            ->label('Segurança')
                            ->options([
                                'smtp' => 'Automática / STARTTLS',
                                'smtps' => 'SSL/TLS',
                            ])
                            ->placeholder('Automática')
                            ->visible(fn (Get $get): bool => $get('mailer') === 'smtp'),
                        TextInput::make('host')
                            ->label('Servidor SMTP')
                            ->default('127.0.0.1')
                            ->required(fn (Get $get): bool => $get('mailer') === 'smtp')
                            ->maxLength(255),
                        TextInput::make('port')
                            ->label('Porta')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535)
                            ->default(2525)
                            ->required(fn (Get $get): bool => $get('mailer') === 'smtp'),
                        TextInput::make('username')
                            ->label('Usuário')
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                            ->required(fn ($livewire, Get $get): bool => $livewire instanceof CreateRecord && $get('mailer') === 'smtp')
                            ->afterStateHydrated(fn (TextInput $component): TextInput => $component->state(null))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('A senha é armazenada criptografada. Deixe em branco ao editar para manter a atual.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Remetente')
                    ->schema([
                        TextInput::make('from_address')
                            ->label('E-mail do remetente')
                            ->email()
                            ->default('hello@example.com')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('from_name')
                            ->label('Nome do remetente')
                            ->default(config('app.name'))
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
