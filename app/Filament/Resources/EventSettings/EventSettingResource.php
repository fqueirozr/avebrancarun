<?php

namespace App\Filament\Resources\EventSettings;

use App\Filament\Resources\EventSettings\Pages\CreateEventSetting;
use App\Filament\Resources\EventSettings\Pages\EditEventSetting;
use App\Filament\Resources\EventSettings\Pages\ListEventSettings;
use App\Filament\Resources\EventSettings\Schemas\EventSettingForm;
use App\Filament\Resources\EventSettings\Tables\EventSettingsTable;
use App\Models\EventSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EventSettingResource extends Resource
{
    protected static ?string $model = EventSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $modelLabel = 'configuracao do evento';

    protected static ?string $pluralModelLabel = 'configuracoes do evento';

    protected static ?string $navigationLabel = 'Evento';

    protected static string|UnitEnum|null $navigationGroup = 'Configuracoes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EventSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventSettingsTable::configure($table);
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
            'index' => ListEventSettings::route('/'),
            'create' => CreateEventSetting::route('/create'),
            'edit' => EditEventSetting::route('/{record}/edit'),
        ];
    }
}
