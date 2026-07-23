<?php

namespace App\Filament\Resources\RaceModalities\Schemas;

use App\Models\RaceModality;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RaceModalityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação da prova')
                    ->description('Defina como a prova será apresentada aos atletas.')
                    ->icon(Heroicon::OutlinedFlag)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Adulto a partir de 16 anos')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('type')
                            ->label('Categoria')
                            ->options(RaceModality::typeOptions())
                            ->required()
                            ->native(false),
                        TextInput::make('distance')
                            ->label('Distância')
                            ->placeholder('6 km')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Descrição curta')
                            ->helperText('Este resumo aparece na apresentação pública da prova.')
                            ->rows(3)
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ])
                    ->columns(2),
                Section::make('Elegibilidade e disponibilidade')
                    ->description('Controle a faixa etária, as vagas e a ordem de exibição.')
                    ->icon(Heroicon::OutlinedUsers)
                    ->schema([
                        TextInput::make('age_start')
                            ->label('Idade mínima')
                            ->integer()
                            ->minValue(0),
                        TextInput::make('age_end')
                            ->label('Idade máxima')
                            ->integer()
                            ->minValue(0)
                            ->gte('age_start'),
                        TextInput::make('max_participants')
                            ->label('Limite de atletas')
                            ->helperText('Deixe em branco para não limitar as inscrições.')
                            ->integer()
                            ->minValue(1),
                        TextInput::make('sort_order')
                            ->label('Ordem de exibição')
                            ->helperText('Também pode ser ajustada arrastando as provas na listagem.')
                            ->integer()
                            ->default(0)
                            ->required()
                            ->minValue(0),
                        Toggle::make('is_active')
                            ->label('Disponível para inscrições')
                            ->helperText('Provas inativas permanecem cadastradas, mas não aparecem para novos atletas.')
                            ->default(true)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Data e percurso')
                    ->description('Reúna horário, mapa, orientações e imagens do trajeto.')
                    ->icon(Heroicon::OutlinedMap)
                    ->schema([
                        DatePicker::make('race_date')
                            ->label('Data da prova')
                            ->native(false),
                        TimePicker::make('race_time')
                            ->label('Horário da largada')
                            ->seconds(false)
                            ->native(false),
                        TextInput::make('google_maps_embed_url')
                            ->label('URL de incorporação do Google Maps')
                            ->helperText('Use o endereço gerado em Compartilhar > Incorporar um mapa.')
                            ->placeholder('https://www.google.com/maps/embed?...')
                            ->url()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        self::richEditor('course_information', 'Informações do percurso', 'Informe detalhes do trajeto, hidratação, altimetria ou avisos'),
                        FileUpload::make('course_images')
                            ->label('Fotos do percurso')
                            ->helperText('Envie até 6 imagens de 4 MB cada. Arraste para definir a ordem.')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->directory('race-modalities/course-images')
                            ->visibility('public')
                            ->maxFiles(6)
                            ->maxSize(4096)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    private static function richEditor(string $name, string $label, string $placeholder): RichEditor
    {
        return RichEditor::make($name)
            ->label($label)
            ->placeholder($placeholder)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link'],
                ['paragraph', 'h2', 'h3'],
                ['alignStart', 'alignCenter', 'alignEnd'],
                ['blockquote', 'bulletList', 'orderedList'],
                ['table'],
                ['undo', 'redo'],
            ])
            ->columnSpanFull();
    }
}
