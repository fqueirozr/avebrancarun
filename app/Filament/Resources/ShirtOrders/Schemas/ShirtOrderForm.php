<?php

namespace App\Filament\Resources\ShirtOrders\Schemas;

use App\Models\ParticipantRegistration;
use App\Models\ShirtOrder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
                    ->label('Item avulso')
                    ->relationship('shirt', 'name')
                    ->required(),
                Select::make('participant_registration_id')
                    ->label('Inscrição')
                    ->relationship('participantRegistration', 'id'),
                TextInput::make('customer_name')
                    ->label('Nome do cliente')
                    ->required(),
                TextInput::make('customer_cpf')
                    ->label('CPF do cliente')
                    ->afterStateHydrated(fn (TextInput $component, ?ShirtOrder $record): TextInput => $component->state(
                        $record?->customer_cpf,
                    ))
                    ->maxLength(11),
                TextInput::make('customer_email')
                    ->label('E-mail do cliente')
                    ->email()
                    ->required(),
                TextInput::make('customer_phone')
                    ->label('Telefone do cliente')
                    ->tel()
                    ->required(),
                Select::make('size')
                    ->label('Tamanho')
                    ->options(ParticipantRegistration::shirtSizeOptions())
                    ->visibleOn('create')
                    ->required(),
                Repeater::make('sizes')
                    ->label('Tamanho de cada camiseta')
                    ->simple(
                        Select::make('value')
                            ->options(ParticipantRegistration::shirtSizeOptions())
                            ->required(),
                    )
                    ->afterStateHydrated(function (Repeater $component, ?ShirtOrder $record): void {
                        if ($record === null) {
                            return;
                        }

                        $sizes = $record->sizes ?: array_fill(0, $record->quantity, $record->size);

                        $component->state(
                            collect($sizes)
                                ->map(fn (string $size): array => ['value' => $size])
                                ->all(),
                        );
                    })
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->visibleOn('edit')
                    ->required(),
                TextInput::make('quantity')
                    ->label('Quantidade')
                    ->disabledOn('edit')
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
                    ->helperText('Comprovante enviado na inscrição vinculada ou no pedido avulso.')
                    ->afterStateHydrated(fn (FileUpload $component, ?ShirtOrder $record): FileUpload => $component->state(
                        $record?->pix_receipt_path ?: $record?->participantRegistration?->pix_receipt_path,
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
