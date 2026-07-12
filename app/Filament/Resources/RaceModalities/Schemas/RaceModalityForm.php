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
use Filament\Schemas\Schema;

class RaceModalityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->placeholder('Adulto a partir de 16 anos')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Tipo')
                    ->options(RaceModality::typeOptions())
                    ->required()
                    ->native(false),
                TextInput::make('age_start')
                    ->label('Idade inicial')
                    ->integer()
                    ->minValue(0),
                TextInput::make('age_end')
                    ->label('Idade final')
                    ->integer()
                    ->minValue(0)
                    ->gte('age_start'),
                TextInput::make('distance')
                    ->label('Distância')
                    ->placeholder('6 km')
                    ->maxLength(255),
                DatePicker::make('race_date')
                    ->label('Data da prova')
                    ->native(false),
                TimePicker::make('race_time')
                    ->label('Horário da prova')
                    ->seconds(false)
                    ->native(false),
                TextInput::make('google_maps_embed_url')
                    ->label('URL do Google Maps')
                    ->placeholder('https://www.google.com/maps/embed?...')
                    ->url()
                    ->maxLength(2048)
                    ->columnSpanFull(),
                TextInput::make('max_participants')
                    ->label('Limite de atletas')
                    ->integer()
                    ->minValue(1),
                TextInput::make('sort_order')
                    ->label('Ordem')
                    ->integer()
                    ->default(0)
                    ->required()
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('Ativa')
                    ->default(true)
                    ->required(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpanFull()
                    ->maxLength(1000),
                self::richEditor('course_information', 'Informações do percurso', 'Informe detalhes do trajeto, hidratação, altimetria ou avisos'),
                FileUpload::make('course_images')
                    ->label('Fotos do percurso')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->disk('public')
                    ->directory('race-modalities/course-images')
                    ->visibility('public')
                    ->maxFiles(6)
                    ->maxSize(4096)
                    ->columnSpanFull(),
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
                ['undo', 'redo'],
            ])
            ->columnSpanFull();
    }
}
