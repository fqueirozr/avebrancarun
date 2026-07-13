<?php

namespace App\Filament\Resources\RaceResults\Tables;

use App\Actions\RecalculateRaceRankings;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RaceResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->modifyQueryUsing(fn ($query) => $query->with('raceModality'))
            ->columns([
                TextColumn::make('overall_rank')
                    ->label('Geral')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}º" : '—')
                    ->sortable(),
                TextColumn::make('athlete_name')
                    ->label('Participante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('raceModality.name')
                    ->label('Prova')
                    ->sortable(),
                TextColumn::make('bib_number')
                    ->label('Nº do peito')
                    ->placeholder('Não informado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('elapsed_time')
                    ->label('Tempo')
                    ->placeholder('Aguardando')
                    ->sortable(),
                TextColumn::make('sex_rank')
                    ->label('Sexo')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}º" : '—'),
                TextColumn::make('category_rank')
                    ->label('Categoria')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}º" : '—'),
                TextColumn::make('result_status')
                    ->label('Situação')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ParticipantRegistration::resultStatusOptions()[$state ?? 'awaiting'])
                    ->color(fn (?string $state): string => match ($state) {
                        'finished' => 'success',
                        'disqualified', 'did_not_finish', 'did_not_start' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('race_modality_id')
                    ->label('Prova')
                    ->options(fn (): array => RaceModality::options()),
                SelectFilter::make('result_status')
                    ->label('Situação')
                    ->options(ParticipantRegistration::resultStatusOptions()),
            ])
            ->defaultSort('elapsed_time')
            ->recordActions([
                Action::make('registerResult')
                    ->label('Informar resultado')
                    ->icon(Heroicon::OutlinedClock)
                    ->fillForm(fn (ParticipantRegistration $record): array => [
                        'bib_number' => $record->bib_number,
                        'elapsed_time' => $record->elapsed_time,
                        'result_category' => $record->result_category,
                        'result_status' => $record->result_status,
                    ])
                    ->schema([
                        TextInput::make('bib_number')
                            ->label('Número do peito')
                            ->required()
                            ->maxLength(255)
                            ->unique(table: ParticipantRegistration::class, column: 'bib_number', ignoreRecord: true),
                        TextInput::make('elapsed_time')
                            ->label('Tempo de corrida')
                            ->placeholder('01:23:45')
                            ->helperText('Use o formato HH:MM:SS.')
                            ->regex('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/')
                            ->required(fn ($get): bool => $get('result_status') === 'finished'),
                        Select::make('result_status')
                            ->label('Situação')
                            ->options(ParticipantRegistration::resultStatusOptions())
                            ->required()
                            ->live(),
                        Select::make('result_category')
                            ->label('Categoria')
                            ->options(ParticipantRegistration::resultCategoryOptions())
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (ParticipantRegistration $record, array $data): void {
                        $record->update($data);

                        app(RecalculateRaceRankings::class)->handle($record);

                        Notification::make()
                            ->success()
                            ->title('Resultado salvo e rankings atualizados')
                            ->send();
                    }),
            ]);
    }
}
