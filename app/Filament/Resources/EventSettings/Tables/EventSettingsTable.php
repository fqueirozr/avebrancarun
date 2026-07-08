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
                TextColumn::make('contact_email')
                    ->label('E-mail')
                    ->placeholder('NÃ£o informado')
                    ->searchable(),
                TextColumn::make('contact_phone')
                    ->label('Telefone')
                    ->placeholder('NÃ£o informado')
                    ->toggleable(),
                TextColumn::make('contact_whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('NÃ£o informado')
                    ->toggleable(),
                TextColumn::make('kit_information')
                    ->label('Kit atleta')
                    ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : str($state)->stripTags()->limit(50)->toString())
                    ->limit(50)
                    ->placeholder('Em definição')
                    ->toggleable(),
                TextColumn::make('regulation')
                    ->label('Regulamento')
                    ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : str($state)->stripTags()->limit(50)->toString())
                    ->limit(50)
                    ->placeholder('Em revisão')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
