<?php

namespace App\Filament\Resources\Pathfinders;

use App\Filament\Resources\Pathfinders\Pages\CreatePathfinder;
use App\Filament\Resources\Pathfinders\Pages\EditPathfinder;
use App\Filament\Resources\Pathfinders\Pages\ListPathfinders;
use App\Filament\Resources\Pathfinders\Schemas\PathfinderForm;
use App\Filament\Resources\Pathfinders\Tables\PathfindersTable;
use App\Models\Pathfinder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PathfinderResource extends Resource
{
    protected static ?string $model = Pathfinder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'desbravador';

    protected static ?string $pluralModelLabel = 'desbravadores';

    protected static ?string $navigationLabel = 'Desbravadores';

    protected static string|UnitEnum|null $navigationGroup = 'Secretaria';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PathfinderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PathfindersTable::configure($table);
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
            'index' => ListPathfinders::route('/'),
            'create' => CreatePathfinder::route('/create'),
            'edit' => EditPathfinder::route('/{record}/edit'),
        ];
    }
}
