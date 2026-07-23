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
                TextInput::make('cpf')
                    ->label('CPF')
                    ->helperText('Informe somente os 11 dígitos.')
                    ->numeric()
                    ->length(11)
                    ->unique(ignoreRecord: true)
                    ->required(),
                Toggle::make('is_active')->label('Ativo')->default(true)->required(),
            ]);
    }
}
