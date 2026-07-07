<?php

namespace App\Filament\Resources\ParticipantRegistrations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParticipantRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('athlete_name')
                    ->label('Nome do atleta')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('birth_date')
                    ->label('Data de nascimento')
                    ->required(),
                TextInput::make('guardian_name')
                    ->label('Responsavel')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->required()
                    ->tel()
                    ->maxLength(30),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('modality')
                    ->label('Modalidade')
                    ->options([
                        'Infantil 6-7 anos - 100 m' => 'Infantil 6-7 anos - 100 m',
                        'Infantil 8-9 anos - 200 m' => 'Infantil 8-9 anos - 200 m',
                        'Infantil 10-11 anos - 300 m' => 'Infantil 10-11 anos - 300 m',
                        'Infantil 12-13 anos - 400 m' => 'Infantil 12-13 anos - 400 m',
                        'Adulto a partir de 14 anos - 3 km' => 'Adulto a partir de 14 anos - 3 km',
                        'Adulto a partir de 16 anos - 6 km' => 'Adulto a partir de 16 anos - 6 km',
                    ])
                    ->required(),
                Select::make('payment_status')
                    ->label('Status do pagamento')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'cancelled' => 'Cancelado',
                    ])
                    ->required(),
                Textarea::make('notes')
                    ->label('Observacoes')
                    ->columnSpanFull()
                    ->maxLength(1000),
            ]);
    }
}
