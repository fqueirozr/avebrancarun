<?php

namespace App\Filament\Resources\RaceModalities;

use App\Filament\Resources\RaceModalities\Pages\CreateRaceModality;
use App\Filament\Resources\RaceModalities\Pages\EditRaceModality;
use App\Filament\Resources\RaceModalities\Pages\ListRaceModalities;
use App\Filament\Resources\RaceModalities\Schemas\RaceModalityForm;
use App\Filament\Resources\RaceModalities\Tables\RaceModalitiesTable;
use App\Models\RaceModality;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RaceModalityResource extends Resource
{
    protected static ?string $model = RaceModality::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'modalidade';

    protected static ?string $pluralModelLabel = 'modalidades';

    protected static ?string $navigationLabel = 'Modalidades';

    protected static string|UnitEnum|null $navigationGroup = 'Configuracoes';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return RaceModalityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RaceModalitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRaceModalities::route('/'),
            'create' => CreateRaceModality::route('/create'),
            'edit' => EditRaceModality::route('/{record}/edit'),
        ];
    }
}
