<?php

namespace App\Filament\Resources\ParticipantRegistrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParticipantRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('athlete_name')
                    ->label('Atleta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('modality')
                    ->label('Modalidade')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('payment_status')
                    ->label('Pagamento')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Pago',
                        'cancelled' => 'Cancelado',
                        default => 'Pendente',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Inscrito em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Pagamento')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'cancelled' => 'Cancelado',
                    ]),
                SelectFilter::make('modality')
                    ->label('Modalidade')
                    ->options([
                        'Infantil 6-7 anos - 100 m' => 'Infantil 6-7 anos - 100 m',
                        'Infantil 8-9 anos - 200 m' => 'Infantil 8-9 anos - 200 m',
                        'Infantil 10-11 anos - 300 m' => 'Infantil 10-11 anos - 300 m',
                        'Infantil 12-13 anos - 400 m' => 'Infantil 12-13 anos - 400 m',
                        'Adulto a partir de 14 anos - 3 km' => 'Adulto a partir de 14 anos - 3 km',
                        'Adulto a partir de 16 anos - 6 km' => 'Adulto a partir de 16 anos - 6 km',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
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
