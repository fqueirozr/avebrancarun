<?php

namespace App\Filament\Resources\RaceModalities\Tables;

use App\Models\RaceModality;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RaceModalitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),
                TextColumn::make('age_start')
                    ->label('Faixa etária')
                    ->formatStateUsing(fn (mixed $state, $record): string => $record->ageRangeLabel())
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('distance')
                    ->label('Distância')
                    ->searchable(),
                TextColumn::make('race_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->placeholder('A confirmar')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('race_time')
                    ->label('Horário')
                    ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : str($state)->substr(0, 5)->toString())
                    ->placeholder('A confirmar')
                    ->toggleable(),
                TextColumn::make('google_maps_embed_url')
                    ->label('Mapa')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Configurado' : 'Pendente')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('course_images')
                    ->label('Fotos')
                    ->formatStateUsing(fn (?array $state): string => count($state ?? []).' foto(s)')
                    ->toggleable(),
                TextColumn::make('max_participants')
                    ->label('Limite de atletas')
                    ->placeholder('Sem limite')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Ativa')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(RaceModality::typeOptions()),
                TernaryFilter::make('is_active')
                    ->label('Ativa'),
            ])
            ->defaultSort('sort_order')
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
