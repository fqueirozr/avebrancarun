<?php

namespace App\Filament\Resources\ShirtOrders\Schemas;

use App\Models\ShirtOrder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShirtOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shirt_id')
                    ->label('Camiseta')
                    ->relationship('shirt', 'name')
                    ->required(),
                Select::make('participant_registration_id')
                    ->label('Inscrição')
                    ->relationship('participantRegistration', 'id'),
                TextInput::make('customer_name')
                    ->label('Nome do cliente')
                    ->required(),
                TextInput::make('customer_email')
                    ->label('E-mail do cliente')
                    ->email()
                    ->required(),
                TextInput::make('customer_phone')
                    ->label('Telefone do cliente')
                    ->tel()
                    ->required(),
                TextInput::make('size')
                    ->label('Tamanho')
                    ->required(),
                TextInput::make('quantity')
                    ->label('Quantidade')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_price')
                    ->label('Valor unitário')
                    ->required()
                    ->numeric()
                    ->prefix('R$'),
                TextInput::make('total_price')
                    ->label('Valor total')
                    ->required()
                    ->numeric()
                    ->prefix('R$'),
                Select::make('payment_status')
                    ->label('Status do pagamento')
                    ->options(ShirtOrder::paymentStatusOptions())
                    ->required()
                    ->default('pending'),
                FileUpload::make('payment_receipt_path')
                    ->label('Comprovante do Pix')
                    ->helperText('Comprovante enviado na inscrição vinculada.')
                    ->afterStateHydrated(fn (FileUpload $component, ?ShirtOrder $record): FileUpload => $component->state(
                        $record?->participantRegistration?->pix_receipt_path,
                    ))
                    ->disk('local')
                    ->visibility('private')
                    ->downloadable()
                    ->openable()
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ]);
    }
}
