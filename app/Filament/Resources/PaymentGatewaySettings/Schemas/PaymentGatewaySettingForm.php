<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentGatewaySettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gateway')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Ativar checkout')
                            ->helperText('Quando ativo, inscrições com valor redirecionam para o checkout do gateway.')
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
                                'sandbox' => 'Sandbox',
                                'production' => 'Produção',
                            ])
                            ->default('sandbox')
                            ->required(),
                        TextInput::make('api_key')
                            ->label('API key')
                            ->password()
                            ->revealable()
                            ->maxLength(65535)
                            ->helperText('No Asaas, gere em Integrações > API Key. A chave fica criptografada no banco.'),
                    ])
                    ->columns(2),
                Section::make('Checkout')
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
                    ->columns(2),
            ]);
    }
}
