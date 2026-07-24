<?php

namespace App\Filament\Resources\ParticipantRegistrations\Schemas;

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ParticipantRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação da inscrição')
                    ->schema([
                        TextInput::make('protocol_number')
                            ->label('Protocolo')
                            ->helperText('Gerado automaticamente ao salvar a inscrição no banco de dados.')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('bib_number')
                            ->label('Número de peito')
                            ->maxLength(50),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Dados do atleta')
                    ->schema([
                        TextInput::make('athlete_name')
                            ->label('Nome do atleta')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DatePicker::make('birth_date')
                            ->label('Data de nascimento')
                            ->required(),
                        Select::make('sex')
                            ->label('Sexo')
                            ->options(ParticipantRegistration::sexOptions())
                            ->required(),
                        TextInput::make('participant_cpf')
                            ->label('CPF do atleta')
                            ->mask('999.999.999-99')
                            ->stripCharacters(['.', '-'])
                            ->extraInputAttributes(['inputmode' => 'numeric'])
                            ->required()
                            ->length(11)
                            ->regex('/^\d{11}$/'),
                        Select::make('shirt_size')
                            ->label('Tamanho da camisa')
                            ->options(ParticipantRegistration::shirtSizeOptions())
                            ->visible(fn (Get $get): bool => (bool) Kit::query()->whereKey($get('kit_id'))->value('has_shirt'))
                            ->required(fn (Get $get): bool => (bool) Kit::query()->whereKey($get('kit_id'))->value('has_shirt'))
                            ->dehydrated(fn (Get $get): bool => (bool) Kit::query()->whereKey($get('kit_id'))->value('has_shirt')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Responsável e contato')
                    ->schema([
                        Toggle::make('filled_by_legal_representative')
                            ->label('Preenchida pelo representante legal')
                            ->columnSpanFull(),
                        TextInput::make('guardian_name')
                            ->label('Responsável legal')
                            ->maxLength(255),
                        TextInput::make('guardian_cpf')
                            ->label('CPF do responsável legal')
                            ->mask('999.999.999-99')
                            ->stripCharacters(['.', '-'])
                            ->extraInputAttributes(['inputmode' => 'numeric'])
                            ->length(11)
                            ->regex('/^\d{11}$/'),
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
                        TextInput::make('emergency_contact_name')
                            ->label('Contato de emergência')
                            ->maxLength(255),
                        TextInput::make('emergency_contact_phone')
                            ->label('Telefone de emergência')
                            ->tel()
                            ->minLength(10)
                            ->maxLength(11),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Prova e pacote')
                    ->schema([
                        Select::make('race_modality_id')
                            ->label('Prova')
                            ->options(fn (): array => RaceModality::options())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('kit_id')
                            ->label('Pacote')
                            ->options(fn (): array => Kit::options())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Cobrança')
                    ->schema([
                        TextInput::make('billing_name')
                            ->label('Nome do pagador')
                            ->maxLength(255),
                        TextInput::make('billing_document')
                            ->label('CPF/CNPJ do pagador')
                            ->extraInputAttributes(['inputmode' => 'numeric'])
                            ->minLength(11)
                            ->maxLength(14)
                            ->regex('/^\d{11}(\d{3})?$/'),
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
                            ->extraInputAttributes(['inputmode' => 'numeric'])
                            ->length(8)
                            ->regex('/^\d{8}$/'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Pagamento')
                    ->schema([
                        Select::make('payment_status')
                            ->label('Status do pagamento')
                            ->options(ParticipantRegistration::paymentStatusOptions())
                            ->required(),
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
                            ->disabled()
                            ->dehydrated(false),
                        FileUpload::make('pix_receipt_path')
                            ->label('Comprovante do Pix')
                            ->disk('local')
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Resultado')
                    ->schema([
                        Select::make('result_status')
                            ->label('Status na prova')
                            ->options(ParticipantRegistration::resultStatusOptions())
                            ->required(),
                        TextInput::make('elapsed_time')
                            ->label('Tempo oficial')
                            ->placeholder('00:42:18')
                            ->regex('/^\d{2}:\d{2}:\d{2}$/')
                            ->helperText('Use o formato HH:MM:SS.'),
                        Select::make('result_category')
                            ->label('Categoria do resultado')
                            ->options(ParticipantRegistration::resultCategoryOptions())
                            ->searchable()
                            ->preload(),
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Aceites')
                    ->schema([
                        TextInput::make('privacy_policy_version')
                            ->label('Versão da política aceita')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('privacy_policy_accepted_at')
                            ->label('Aceite da política em')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsed(),
            ]);
    }
}
