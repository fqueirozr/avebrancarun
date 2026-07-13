<?php

namespace App\Filament\Resources\EventSettings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados gerais')
                    ->schema([
                        TextInput::make('event_date')
                            ->label('Data')
                            ->placeholder('A confirmar')
                            ->maxLength(255),
                        TextInput::make('event_location')
                            ->label('Local')
                            ->placeholder('A confirmar')
                            ->maxLength(255),
                        DateTimePicker::make('registration_deadline')
                            ->label('Prazo máximo para inscrição')
                            ->seconds(false)
                            ->native(false)
                            ->helperText('Após esta data e horário, novas inscrições serão bloqueadas.'),
                        TextInput::make('max_registrations')
                            ->label('Limite total de inscrições')
                            ->integer()
                            ->minValue(1)
                            ->helperText('Deixe vazio para não aplicar um limite geral.'),
                        TextInput::make('organizer_legal_name')
                            ->label('Razão social do organizador')
                            ->placeholder('Nome empresarial responsável pelo evento')
                            ->maxLength(255),
                        TextInput::make('organizer_cnpj')
                            ->label('CNPJ do organizador')
                            ->placeholder('00.000.000/0000-00')
                            ->mask('99.999.999/9999-99')
                            ->regex('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/')
                            ->validationMessages([
                                'regex' => 'Informe o CNPJ no formato 00.000.000/0000-00.',
                            ])
                            ->maxLength(18)
                            ->helperText('Este dado será exibido no rodapé público para identificar o responsável pelo evento.'),
                        TextInput::make('contact_email')
                            ->label('E-mail de contato')
                            ->placeholder('contato@evento.com.br')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('contact_phone')
                            ->label('Telefone do evento')
                            ->placeholder('(00) 0000-0000')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('contact_whatsapp')
                            ->label('WhatsApp do evento')
                            ->placeholder('(00) 00000-0000')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Informações da prova')
                    ->schema([
                        self::richEditor('general_information', 'Informações gerais', 'Informe data, local, horários e demais orientações gerais'),
                        self::richEditor('kit_information', 'Retirada de kit', 'Informe os itens do kit ou orientações de retirada'),
                        self::richEditor('baggage_storage_information', 'Guarda-volumes', 'Informe se haverá guarda-volumes e quais são as regras'),
                        self::richEditor('start_groups_information', 'Pelotões de largada', 'Informe os grupos, horários e organização da largada'),
                        self::richEditor('timing_information', 'Cronometragem', 'Informe como funcionará a apuração e divulgação dos resultados'),
                        self::richEditor('special_registrations_information', 'Inscrições especiais', 'Informe regras para PCD, cortesias ou necessidades específicas'),
                        self::richEditor('regulation', 'Regulamento', 'Informe as principais regras do evento'),
                    ])
                    ->columns(2)
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
                ['table'],
                ['undo', 'redo'],
            ])
            ->columnSpanFull();
    }
}
