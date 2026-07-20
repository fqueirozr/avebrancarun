<?php

namespace App\Filament\Resources\Shirts;

use App\Filament\Resources\Shirts\Pages\CreateShirt;
use App\Filament\Resources\Shirts\Pages\EditShirt;
use App\Filament\Resources\Shirts\Pages\ListShirts;
use App\Filament\Resources\Shirts\Schemas\ShirtForm;
use App\Filament\Resources\Shirts\Tables\ShirtsTable;
use App\Models\Shirt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShirtResource extends Resource
{
    protected static ?string $model = Shirt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'camiseta';

    protected static ?string $pluralModelLabel = 'camisetas';

    protected static ?string $navigationLabel = 'Camisetas';

    public static function form(Schema $schema): Schema
    {
        return ShirtForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShirtsTable::configure($table);
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
            'index' => ListShirts::route('/'),
            'create' => CreateShirt::route('/create'),
            'edit' => EditShirt::route('/{record}/edit'),
        ];
    }
}
