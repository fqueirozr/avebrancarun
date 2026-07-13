<?php

namespace App\Filament\Resources\Kits;

use App\Filament\Resources\Kits\Pages\CreateKit;
use App\Filament\Resources\Kits\Pages\EditKit;
use App\Filament\Resources\Kits\Pages\ListKits;
use App\Filament\Resources\Kits\Schemas\KitForm;
use App\Filament\Resources\Kits\Tables\KitsTable;
use App\Models\Kit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KitResource extends Resource
{
    protected static ?string $model = Kit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $modelLabel = 'kit';

    protected static ?string $pluralModelLabel = 'kits';

    protected static ?string $navigationLabel = 'Kits';

    protected static string|UnitEnum|null $navigationGroup = 'Configuração';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return KitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KitsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKits::route('/'),
            'create' => CreateKit::route('/create'),
            'edit' => EditKit::route('/{record}/edit'),
        ];
    }
}
