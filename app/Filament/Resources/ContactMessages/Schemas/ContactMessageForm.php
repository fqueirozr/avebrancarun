<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
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
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('subject')
                    ->label('Assunto')
                    ->maxLength(255),
                DateTimePicker::make('read_at')
                    ->label('Lida em'),
                Textarea::make('message')
                    ->label('Mensagem')
                    ->required()
                    ->columnSpanFull()
                    ->rows(6)
                    ->maxLength(2000),
            ]);
    }
}
