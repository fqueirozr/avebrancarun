<?php

namespace App\Filament\Resources\EventSettings\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('event_date')
                    ->label('Data')
                    ->placeholder('A confirmar')
                    ->maxLength(255),
                TextInput::make('event_location')
                    ->label('Local')
                    ->placeholder('A confirmar')
                    ->maxLength(255),
                TextInput::make('contact_email')
                    ->label('E-mail de contato')
                    ->placeholder('contato@evento.com.br')
                    ->email()
                    ->maxLength(255),
                TextInput::make('contact_phone')
                    ->label('Telefone do evento')
                    ->placeholder('(00) 0000-0000')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('contact_whatsapp')
                    ->label('WhatsApp do evento')
                    ->placeholder('(00) 00000-0000')
                    ->tel()
                    ->maxLength(255),
                RichEditor::make('kit_information')
                    ->label('Kit atleta')
                    ->placeholder('Informe os itens do kit ou orientações de retirada')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'link'],
                        ['paragraph', 'h2', 'h3'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['blockquote', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->columnSpanFull()
                    ->maxLength(2000),
                RichEditor::make('regulation')
                    ->label('Regulamento')
                    ->placeholder('Informe as principais regras do evento')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'link'],
                        ['paragraph', 'h2', 'h3'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['blockquote', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
