<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PaymentGatewaySettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pix manual')
                    ->description('Exiba a chave Pix ao participante e receba o comprovante para análise.')
                    ->schema([
                        Toggle::make('manual_pix_enabled')
                            ->label('Ativar Pix manual')
                            ->default(false),
                        TextInput::make('pix_key')
                            ->label('Chave Pix')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                        TextInput::make('pix_receiver_name')
                            ->label('Nome do recebedor')
                            ->helperText('Nome exibido pelo banco no QR Pix (até 25 caracteres).')
                            ->maxLength(25)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                        TextInput::make('pix_receiver_city')
                            ->label('Cidade do recebedor')
                            ->helperText('Cidade exibida pelo banco no QR Pix (até 15 caracteres).')
                            ->maxLength(15)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                        TextInput::make('pix_bank')
                            ->label('Banco')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                        TextInput::make('pix_agency')
                            ->label('Agência')
                            ->maxLength(20)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                        TextInput::make('pix_account')
                            ->label('Conta')
                            ->helperText('Informe o número da conta com o dígito, quando houver.')
                            ->maxLength(30)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                        TextInput::make('pix_account_holder')
                            ->label('Titular da conta')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => (bool) $get('manual_pix_enabled')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Pagamento on-line')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Ativar pagamento on-line')
                            ->helperText('Quando ativo, inscrições com valor redirecionam para a página de pagamento do provedor.')
                            ->default(false),
                        Select::make('gateway')
                            ->label('Provedor')
                            ->options([
                                'asaas' => 'Asaas',
                            ])
                            ->default('asaas')
                            ->required(),
                        Select::make('environment')
                            ->label('Ambiente')
                            ->options([
                                'sandbox' => 'Testes (sandbox)',
                                'production' => 'Produção',
                            ])
                            ->default('sandbox')
                            ->required(),
                        TextInput::make('api_key')
                            ->label('Chave da API')
                            ->password()
                            ->revealable()
                            ->maxLength(65535)
                            ->helperText('No Asaas, gere em Integrações > Chave de API. A chave fica criptografada no banco.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Cobrança')
                    ->schema([
                        TextInput::make('checkout_minutes_to_expire')
                            ->label('Expiração em minutos')
                            ->numeric()
                            ->minValue(10)
                            ->maxValue(1440)
                            ->default(60)
                            ->required(),
                        CheckboxList::make('billing_types')
                            ->label('Meios de pagamento')
                            ->options([
                                'PIX' => 'Pix',
                                'CREDIT_CARD' => 'Cartão de crédito',
                            ])
                            ->default(['PIX', 'CREDIT_CARD'])
                            ->columns(2)
                            ->required(),
                        CheckboxList::make('charge_types')
                            ->label('Tipo de cobrança')
                            ->options([
                                'DETACHED' => 'Avulsa',
                                'INSTALLMENT' => 'Parcelada',
                                'RECURRENT' => 'Recorrente',
                            ])
                            ->default(['DETACHED'])
                            ->columns(3)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
