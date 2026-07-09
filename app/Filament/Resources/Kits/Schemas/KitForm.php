<?php

namespace App\Filament\Resources\Kits\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('photo_path')
                    ->label('Foto')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('kits')
                    ->visibility('public')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Valor')
                    ->numeric()
                    ->prefix('R$')
                    ->required()
                    ->minValue(0),
                TextInput::make('sort_order')
                    ->label('Ordem')
                    ->integer()
                    ->default(0)
                    ->required()
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true)
                    ->required(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpanFull()
                    ->maxLength(1000),
            ]);
    }
}
