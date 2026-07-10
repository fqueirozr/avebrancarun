<?php

namespace App\Filament\Resources\ParticipantRegistrations\Schemas;

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ParticipantRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('protocol_number')
                    ->label('Protocolo')
                    ->helperText('Gerado automaticamente ao salvar a inscrição no banco de dados.')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('athlete_name')
                    ->label('Nome do atleta')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('birth_date')
                    ->label('Data de nascimento')
                    ->required(),
                Select::make('sex')
                    ->label('Sexo')
                    ->options(ParticipantRegistration::sexOptions())
                    ->required(),
                TextInput::make('participant_cpf')
                    ->label('CPF do atleta')
                    ->numeric()
                    ->required()
                    ->minLength(11)
                    ->maxLength(11),
                TextInput::make('guardian_name')
                    ->label('Responsável Legal')
                    ->maxLength(255),
                TextInput::make('guardian_cpf')
                    ->label('CPF do responsável legal')
                    ->numeric()
                    ->minLength(11)
                    ->maxLength(11),
                Toggle::make('filled_by_legal_representative')
                    ->label('Preenchida pelo representante legal'),
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
                    ->label('Endereço do pagador')
                    ->maxLength(255),
                TextInput::make('billing_address_number')
                    ->label('Número')
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
                    ->label('Prova')
                    ->options(fn (): array => RaceModality::options())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('kit_id')
                    ->label('Kit')
                    ->options(fn (): array => Kit::options())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('payment_status')
                    ->label('Status do pagamento')
                    ->options(ParticipantRegistration::paymentStatusOptions())
                    ->required(),
                TextInput::make('bib_number')
                    ->label('Número de peito')
                    ->maxLength(50),
                Select::make('result_status')
                    ->label('Status na prova')
                    ->options(ParticipantRegistration::resultStatusOptions())
                    ->required(),
                TextInput::make('elapsed_time')
                    ->label('Tempo oficial')
                    ->placeholder('00:42:18')
                    ->regex('/^\d{2}:\d{2}:\d{2}$/')
                    ->helperText('Use o formato HH:MM:SS.'),
                TextInput::make('result_category')
                    ->label('Categoria do resultado')
                    ->maxLength(100),
                TextInput::make('overall_rank')
                    ->label('Classificação geral')
                    ->numeric()
                    ->minValue(1),
                TextInput::make('sex_rank')
                    ->label('Classificação por sexo')
                    ->numeric()
                    ->minValue(1),
                TextInput::make('category_rank')
                    ->label('Classificação na categoria')
                    ->numeric()
                    ->minValue(1),
                TextInput::make('payment_gateway')
                    ->label('Gateway')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('payment_gateway_reference')
                    ->label('Referência do gateway')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('payment_checkout_url')
                    ->label('Link do checkout')
                    ->url()
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('notes')
                    ->label('Observações')
                    ->columnSpanFull()
                    ->maxLength(1000),
                TextInput::make('emergency_contact_name')
                    ->label('Contato de emergência')
                    ->maxLength(255),
                TextInput::make('emergency_contact_phone')
                    ->label('Telefone de emergência')
                    ->tel()
                    ->minLength(10)
                    ->maxLength(11),
                Textarea::make('health_notes')
                    ->label('Saúde e emergência')
                    ->columnSpanFull()
                    ->maxLength(1000),
                Toggle::make('promotional_opt_in')
                    ->label('Aceitou comunicações promocionais'),
                TextInput::make('privacy_policy_version')
                    ->label('Versão da política aceita')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('privacy_policy_accepted_at')
                    ->label('Aceite da política em')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
