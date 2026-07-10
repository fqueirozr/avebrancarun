<?php

namespace App\Filament\Resources\RaceResults;

use App\Filament\Resources\RaceResults\Pages\ListRaceResults;
use App\Filament\Resources\RaceResults\Tables\RaceResultsTable;
use App\Models\ParticipantRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RaceResultResource extends Resource
{
    protected static ?string $model = ParticipantRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $modelLabel = 'resultado';

    protected static ?string $pluralModelLabel = 'resultados';

    protected static ?string $navigationLabel = 'Resultados e rankings';

    protected static ?string $slug = 'resultados';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return RaceResultsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRaceResults::route('/'),
        ];
    }
}
