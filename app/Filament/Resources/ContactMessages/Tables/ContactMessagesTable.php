<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->placeholder('NÃ£o informado')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Assunto')
                    ->placeholder('Sem assunto')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('message')
                    ->label('Mensagem')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === null ? 'Nova' : 'Lida')
                    ->color(fn (?string $state): string => $state === null ? 'warning' : 'success'),
                TextColumn::make('created_at')
                    ->label('Enviada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('mark_as_read')
                    ->label('Marcar como lida')
                    ->visible(fn (ContactMessage $record): bool => $record->read_at === null)
                    ->action(fn (ContactMessage $record): bool => $record->update(['read_at' => now()])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
