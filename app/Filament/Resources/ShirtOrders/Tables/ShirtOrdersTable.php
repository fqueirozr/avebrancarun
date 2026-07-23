<?php

namespace App\Filament\Resources\ShirtOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShirtOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shirt.name')
                    ->label('Item avulso')
                    ->searchable(),
                TextColumn::make('participantRegistration.id')
                    ->label('Inscrição')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Nome do cliente')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('E-mail do cliente')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Telefone do cliente')
                    ->searchable(),
                TextColumn::make('size')
                    ->label('Tamanho')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label('Valor unitário')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Valor total')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Status do pagamento')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
