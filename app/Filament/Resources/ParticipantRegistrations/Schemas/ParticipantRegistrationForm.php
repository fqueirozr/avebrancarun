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
                TextInput::make('participant_cpf')
                    ->label('CPF do participante')
                    ->numeric()
                    ->required()
                    ->minLength(11)
                    ->maxLength(11),
                TextInput::make('guardian_name')
                    ->label('Responsavel')
                    ->maxLength(255),
                TextInput::make('guardian_cpf')
                    ->label('CPF do responsavel')
                    ->numeric()
                    ->minLength(11)
                    ->maxLength(11),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->required()
                    ->tel()
                    ->minLength(10)
                    ->maxLength(11),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('billing_document')
                    ->label('CPF/CNPJ do pagador')
                    ->numeric()
                    ->minLength(11)
                    ->maxLength(14),
                TextInput::make('billing_name')
                    ->label('Nome do pagador')
                    ->maxLength(255),
                TextInput::make('billing_address')
                    ->label('Endereco do pagador')
                    ->maxLength(255),
                TextInput::make('billing_address_number')
                    ->label('Numero')
                    ->maxLength(20),
                TextInput::make('billing_province')
                    ->label('Bairro')
                    ->maxLength(255),
                TextInput::make('billing_postal_code')
                    ->label('CEP')
                    ->numeric()
                    ->minLength(8)
                    ->maxLength(8),
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
                TextInput::make('payment_gateway')
                    ->label('Gateway')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('payment_gateway_reference')
                    ->label('Referencia do gateway')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('payment_checkout_url')
                    ->label('Link do checkout')
                    ->url()
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('notes')
                    ->label('Observacoes')
                    ->columnSpanFull()
                    ->maxLength(1000),
            ]);
    }
}
