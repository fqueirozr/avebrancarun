<?php

namespace App\Filament\Resources\ParticipantRegistrations\Tables;

use App\Mail\ParticipantRegistrationUpdated;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ParticipantRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s')
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
                TextColumn::make('participant_cpf')
                    ->label('CPF participante')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('guardian_cpf')
                    ->label('CPF responsavel')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->formatStateUsing(fn (string $state): string => ParticipantRegistration::paymentStatusOptions()[$state] ?? 'Pendente')
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('payment_gateway')
                    ->label('Gateway')
                    ->placeholder('Manual')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_gateway_reference')
                    ->label('Ref. gateway')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Inscrito em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Pagamento')
                    ->options(ParticipantRegistration::paymentStatusOptions()),
                SelectFilter::make('modality')
                    ->label('Modalidade')
                    ->options(fn (): array => RaceModality::options())
                    ->query(function ($query, array $data) {
                        if (blank($data['value'])) {
                            return $query;
                        }

                        $modality = RaceModality::query()->find($data['value']);

                        return $query->where('modality', $modality?->displayName());
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('cancel')
                    ->label('Cancelar inscricao')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar inscricao')
                    ->modalDescription('A inscricao sera marcada como cancelada e o participante recebera um email de atualizacao.')
                    ->visible(fn (ParticipantRegistration $record): bool => $record->payment_status !== 'cancelled')
                    ->action(function (ParticipantRegistration $record): void {
                        $record->forceFill([
                            'payment_status' => 'cancelled',
                        ])->save();

                        Mail::to($record->email)->send(new ParticipantRegistrationUpdated($record));

                        Notification::make()
                            ->success()
                            ->title('Inscricao cancelada')
                            ->body('O participante recebeu um email de atualizacao.')
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
