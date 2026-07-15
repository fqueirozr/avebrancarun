<?php

namespace App\Filament\Resources\Pathfinders\Tables;

use App\Models\Pathfinder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PathfindersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('registration.kit'))
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                TextColumn::make('code')->label('Código')->copyable()->searchable(),
                TextColumn::make('referrals_count')->label('Indicações')->counts('referrals'),
                TextColumn::make('upgrade_level')
                    ->label('Nível')
                    ->state(fn (Pathfinder $record): int => $record->upgradeLevel()),
                TextColumn::make('upgrade_contents')
                    ->label('Upgrades adquiridos')
                    ->state(function (Pathfinder $record): string {
                        $contents = $record->upgradeContents();

                        return $contents === [] ? 'Nenhum' : implode(' | ', $contents);
                    })
                    ->wrap(),
                IconColumn::make('is_active')->label('Ativo')->boolean(),
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
