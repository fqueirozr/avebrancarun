<?php

namespace App\Filament\Resources\ParticipantRegistrations;

use App\Filament\Resources\ParticipantRegistrations\Pages\EditParticipantRegistration;
use App\Filament\Resources\ParticipantRegistrations\Pages\ListParticipantRegistrations;
use App\Filament\Resources\ParticipantRegistrations\Schemas\ParticipantRegistrationForm;
use App\Filament\Resources\ParticipantRegistrations\Tables\ParticipantRegistrationsTable;
use App\Models\ParticipantRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ParticipantRegistrationResource extends Resource
{
    protected static ?string $model = ParticipantRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'inscricao';

    protected static ?string $pluralModelLabel = 'inscricoes';

    protected static ?string $navigationLabel = 'Inscricoes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ParticipantRegistrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParticipantRegistrationsTable::configure($table);
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
            'index' => ListParticipantRegistrations::route('/'),
            'edit' => EditParticipantRegistration::route('/{record}/edit'),
        ];
    }
}
