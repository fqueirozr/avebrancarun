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
use Illuminate\Database\Eloquent\Builder;

class RaceModalitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('participantRegistrations'))
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Prova')
                    ->description(fn (RaceModality $record): string => collect([
                        $record->distance,
                        $record->ageRangeLabel(),
                    ])->filter()->join(' • '))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Infantil' => 'info',
                        'Juvenil' => 'warning',
                        'Adulto' => 'success',
                        'Master' => 'primary',
                        'PCD' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('race_date')
                    ->label('Largada')
                    ->date('d/m/Y')
                    ->description(fn (RaceModality $record): ?string => filled($record->race_time)
                        ? str($record->race_time)->substr(0, 5)->prepend('Às ')->toString()
                        : null)
                    ->placeholder('A confirmar')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('participant_registrations_count')
                    ->label('Inscrições')
                    ->formatStateUsing(fn (int $state, RaceModality $record): string => $record->max_participants === null
                        ? (string) $state
                        : "{$state} / {$record->max_participants}")
                    ->description(fn (RaceModality $record): string => $record->max_participants === null ? 'Sem limite' : 'Vagas ocupadas')
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('google_maps_embed_url')
                    ->label('Mapa')
                    ->boolean(fn (?string $state): bool => filled($state))
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Disponível')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Categoria')
                    ->options(RaceModality::typeOptions()),
                TernaryFilter::make('is_active')
                    ->label('Disponível'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateIcon('heroicon-o-flag')
            ->emptyStateHeading('Nenhuma prova cadastrada')
            ->emptyStateDescription('Cadastre a primeira prova para disponibilizá-la aos atletas.')
            ->recordActions([
                EditAction::make()
                    ->label('Editar prova'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
