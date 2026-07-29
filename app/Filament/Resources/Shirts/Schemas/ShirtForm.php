<?php

namespace App\Filament\Resources\Shirts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ShirtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Apresentação do item')
                    ->description('Defina como o item avulso será apresentado aos compradores.')
                    ->icon(Heroicon::OutlinedShoppingBag)
                    ->key('shirt-presentation')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->columnSpanFull()
                            ->maxLength(2000),
                        FileUpload::make('photo_path')
                            ->label('Foto')
                            ->helperText('Imagem usada na loja e durante a inscrição.')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('shirts')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Valores')
                    ->description('Configure os preços de venda e os adicionais por tamanho.')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->key('shirt-pricing')
                    ->schema([
                        TextInput::make('price')
                            ->label('Valor na loja')
                            ->helperText('Valor do item comprado separadamente.')
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->minValue(0),
                        TextInput::make('registration_price')
                            ->label('Valor junto da inscrição')
                            ->helperText('Deixe vazio para usar o mesmo valor da loja.')
                            ->numeric()
                            ->prefix('R$')
                            ->minValue(0),
                        TextInput::make('size_2xl_surcharge')
                            ->label('Adicional 2XG')
                            ->helperText('Somado ao valor da camiseta para este tamanho.')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->required()
                            ->minValue(0),
                        TextInput::make('size_3xl_surcharge')
                            ->label('Adicional 3XG')
                            ->helperText('Somado ao valor da camiseta para este tamanho.')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->required()
                            ->minValue(0),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),
                Section::make('Estoque e disponibilidade')
                    ->description('Controle o saldo disponível e a exibição do item para novas compras.')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->key('shirt-availability')
                    ->schema([
                        TextInput::make('stock_quantity')
                            ->label('Estoque')
                            ->helperText('Deixe vazio para estoque ilimitado.')
                            ->integer()
                            ->minValue(0),
                        Toggle::make('is_active')
                            ->label('Disponível para venda')
                            ->helperText('Itens inativos permanecem cadastrados, mas não aparecem para novas compras.')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
