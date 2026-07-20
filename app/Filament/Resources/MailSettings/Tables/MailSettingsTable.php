<?php

namespace App\Filament\Resources\MailSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MailSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mailer')
                    ->label('Método')
                    ->formatStateUsing(fn (string $state): string => $state === 'smtp' ? 'SMTP' : 'Log')
                    ->badge(),
                TextColumn::make('host')
                    ->label('Servidor'),
                TextColumn::make('from_address')
                    ->label('Remetente'),
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
