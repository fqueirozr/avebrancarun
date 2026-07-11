<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire): bool => $livewire instanceof CreateRecord)
                    ->confirmed()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('Confirmar senha')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire): bool => $livewire instanceof CreateRecord)
                    ->dehydrated(false),
            ]);
    }
}
