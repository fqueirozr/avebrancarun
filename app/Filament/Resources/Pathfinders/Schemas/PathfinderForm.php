<?php

namespace App\Filament\Resources\Pathfinders\Schemas;

use App\Models\Pathfinder;
use Filament\Forms\Components\Placeholder;
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
                Placeholder::make('upgrade_level')
                    ->label('Nível atual')
                    ->content(fn (?Pathfinder $record): string => (string) ($record?->upgradeLevel() ?? 0)),
                Placeholder::make('upgrade_contents')
                    ->label('Upgrades adquiridos')
                    ->content(function (?Pathfinder $record): string {
                        $contents = $record?->upgradeContents() ?? [];

                        return $contents === [] ? 'Nenhum upgrade adquirido até o momento.' : implode("\n", $contents);
                    })
                    ->columnSpanFull(),
                Toggle::make('is_active')->label('Ativo')->default(true)->required(),
            ]);
    }
}
