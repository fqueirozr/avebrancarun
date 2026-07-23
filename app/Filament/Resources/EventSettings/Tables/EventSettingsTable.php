<?php

namespace App\Filament\Resources\EventSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')
                    ->label('Data')
                    ->placeholder('A confirmar')
                    ->searchable(),
                TextColumn::make('event_location')
                    ->label('Local')
                    ->placeholder('A confirmar')
                    ->searchable(),
                TextColumn::make('general_information')
                    ->label('Informações gerais')
                    ->formatStateUsing(fn (?string $state): ?string => self::plainText($state))
                    ->limit(50)
                    ->placeholder('Não informado')
                    ->toggleable(),
                TextColumn::make('kit_information')
                    ->label('Retirada de pacote')
                    ->formatStateUsing(fn (?string $state): ?string => self::plainText($state))
                    ->limit(50)
                    ->placeholder('Em definição')
                    ->toggleable(),
                TextColumn::make('contact_email')
                    ->label('E-mail')
                    ->placeholder('Não informado')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    private static function plainText(?string $state): ?string
    {
        return $state === null ? null : str($state)->stripTags()->limit(50)->toString();
    }
}
