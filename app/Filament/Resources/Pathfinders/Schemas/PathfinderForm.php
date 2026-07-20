<?php

namespace App\Filament\Resources\Pathfinders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PathfinderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nome')->required()->maxLength(255),
                TextInput::make('code')->label('Código')->helperText('Gerado automaticamente com 4 dígitos.')->disabled()->dehydrated(false),
                Toggle::make('is_active')->label('Ativo')->default(true)->required(),
            ]);
    }
}
