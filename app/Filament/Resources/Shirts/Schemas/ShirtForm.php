<?php

namespace App\Filament\Resources\Shirts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShirtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nome')->required()->maxLength(255),
                Textarea::make('description')->label('Descrição')->columnSpanFull()->maxLength(2000),
                FileUpload::make('photo_path')->label('Foto')->image()->directory('shirts'),
                TextInput::make('price')->label('Valor')->numeric()->prefix('R$')->required()->minValue(0),
                TextInput::make('stock_quantity')->label('Estoque')->integer()->minValue(0)->helperText('Deixe vazio para estoque ilimitado.'),
                Toggle::make('is_active')->label('Ativa')->default(true)->required(),
            ]);
    }
}
