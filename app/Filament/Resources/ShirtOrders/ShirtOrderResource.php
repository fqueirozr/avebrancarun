<?php

namespace App\Filament\Resources\ShirtOrders;

use App\Filament\Resources\ShirtOrders\Pages\CreateShirtOrder;
use App\Filament\Resources\ShirtOrders\Pages\EditShirtOrder;
use App\Filament\Resources\ShirtOrders\Pages\ListShirtOrders;
use App\Filament\Resources\ShirtOrders\Pages\PrintShirtOrders;
use App\Filament\Resources\ShirtOrders\Schemas\ShirtOrderForm;
use App\Filament\Resources\ShirtOrders\Tables\ShirtOrdersTable;
use App\Models\ShirtOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShirtOrderResource extends Resource
{
    protected static ?string $model = ShirtOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $modelLabel = 'pedido de camiseta';

    protected static ?string $pluralModelLabel = 'pedidos de camisetas';

    protected static ?string $navigationLabel = 'Pedidos de camisetas';

    protected static string|UnitEnum|null $navigationGroup = 'Secretaria';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ShirtOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShirtOrdersTable::configure($table);
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
            'index' => ListShirtOrders::route('/'),
            'print' => PrintShirtOrders::route('/print'),
            'create' => CreateShirtOrder::route('/create'),
            'edit' => EditShirtOrder::route('/{record}/edit'),
        ];
    }
}
