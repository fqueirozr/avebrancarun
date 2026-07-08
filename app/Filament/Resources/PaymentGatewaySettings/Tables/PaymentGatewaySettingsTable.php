<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentGatewaySettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gateway')
                    ->label('Provedor')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                IconColumn::make('is_enabled')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('environment')
                    ->label('Ambiente')
                    ->badge(),
                TextColumn::make('checkout_minutes_to_expire')
                    ->label('Expiracao')
                    ->suffix(' min'),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
