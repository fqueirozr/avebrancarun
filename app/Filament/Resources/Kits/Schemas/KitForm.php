<?php

namespace App\Filament\Resources\Kits\Schemas;

use App\Models\Kit;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class KitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Apresentação do pacote')
                    ->description('Defina o nome, o tipo e as informações exibidas aos participantes.')
                    ->icon(Heroicon::OutlinedShoppingBag)
                    ->key('kit-presentation')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Tipo')
                            ->options(Kit::typeOptions())
                            ->required()
                            ->default(Kit::TypeStandard)
                            ->native(false),
                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->columnSpanFull()
                            ->maxLength(1000),
                        FileUpload::make('photo_path')
                            ->label('Foto')
                            ->helperText('Imagem usada na apresentação pública do pacote.')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('kits')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Valores')
                    ->description('Configure o preço principal e os adicionais por tamanho.')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->key('kit-pricing')
                    ->schema([
                        TextInput::make('price')
                            ->label('Valor')
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->minValue(0),
                        TextInput::make('size_2xl_surcharge')
                            ->label('Adicional 2XG')
                            ->helperText('Somado ao pacote quando a camiseta inclusa for 2XG.')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->required()
                            ->minValue(0),
                        TextInput::make('size_3xl_surcharge')
                            ->label('Adicional 3XG')
                            ->helperText('Somado ao pacote quando a camiseta inclusa for 3XG.')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->required()
                            ->minValue(0),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->columnSpanFull(),
                Section::make('Disponibilidade')
                    ->description('Controle quantidade, exibição e conteúdo incluído no pacote.')
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                    ->key('kit-availability')
                    ->schema([
                        TextInput::make('max_quantity')
                            ->label('Quantidade máxima')
                            ->helperText('Deixe em branco para não limitar este pacote.')
                            ->integer()
                            ->minValue(1),
                        TextInput::make('sort_order')
                            ->label('Ordem de exibição')
                            ->helperText('Também pode ser ajustada arrastando os pacotes na listagem.')
                            ->integer()
                            ->default(0)
                            ->required()
                            ->minValue(0),
                        Toggle::make('has_shirt')
                            ->label('Inclui camiseta')
                            ->default(true)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Disponível para inscrições')
                            ->helperText('Pacotes inativos permanecem cadastrados, mas não aparecem para novas inscrições.')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Regras do pacote')
                    ->description('Informe as regras adicionais exibidas no modal antes da escolha.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->key('kit-rules')
                    ->schema([
                        RichEditor::make('rules')
                            ->label('Regras exibidas no modal')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
