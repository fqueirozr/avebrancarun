<?php

namespace App\Filament\Resources\ParticipantRegistrations\Schemas;

use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
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
                Select::make('race_modality_id')
                    ->label('Modalidade')
                    ->options(fn (): array => RaceModality::options())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('payment_status')
                    ->label('Status do pagamento')
                    ->options(ParticipantRegistration::paymentStatusOptions())
                    ->required(),
                Textarea::make('notes')
                    ->label('Observacoes')
                    ->columnSpanFull()
                    ->maxLength(1000),
            ]);
    }
}
