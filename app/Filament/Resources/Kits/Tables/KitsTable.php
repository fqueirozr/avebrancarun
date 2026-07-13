<?php

namespace App\Filament\Resources\Kits\Tables;

use App\Models\Kit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class KitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')->label('Tipo')->badge()->formatStateUsing(fn (string $state): string => Kit::typeOptions()[$state] ?? $state),
                TextColumn::make('price')
                    ->label('Valor')
                    ->formatStateUsing(fn (string $state): string => Number::currency((float) $state, 'BRL', 'pt_BR'))
                    ->sortable(),
                TextColumn::make('max_quantity')
                    ->label('Limite')
                    ->placeholder('Sem limite')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Ativo'),
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
