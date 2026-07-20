<?php

namespace App\Filament\Resources\MailSettings;

use App\Filament\Resources\MailSettings\Pages\CreateMailSetting;
use App\Filament\Resources\MailSettings\Pages\EditMailSetting;
use App\Filament\Resources\MailSettings\Pages\ListMailSettings;
use App\Filament\Resources\MailSettings\Schemas\MailSettingForm;
use App\Filament\Resources\MailSettings\Tables\MailSettingsTable;
use App\Models\MailSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MailSettingResource extends Resource
{
    protected static ?string $model = MailSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $modelLabel = 'configuração de e-mail';

    protected static ?string $pluralModelLabel = 'configurações de e-mail';

    protected static ?string $navigationLabel = 'E-mail';

    protected static string|UnitEnum|null $navigationGroup = 'Configuração';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return parent::canCreate() && MailSetting::query()->doesntExist();
    }

    public static function form(Schema $schema): Schema
    {
        return MailSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailSettingsTable::configure($table);
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
            'index' => ListMailSettings::route('/'),
            'create' => CreateMailSetting::route('/create'),
            'edit' => EditMailSetting::route('/{record}/edit'),
        ];
    }
}
