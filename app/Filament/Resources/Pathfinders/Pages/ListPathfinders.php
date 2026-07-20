<?php

namespace App\Filament\Resources\Pathfinders\Pages;

use App\Actions\ImportPathfinders;
use App\Filament\Resources\Pathfinders\PathfinderResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListPathfinders extends ListRecords
{
    protected static string $resource = PathfinderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Importar planilha')
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
                    FileUpload::make('file')
                        ->label('Arquivo do Excel')
                        ->helperText('Use a primeira coluna, com o cabeçalho opcional “Nome”.')
                        ->disk('local')
                        ->directory('imports/pathfinders')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->maxSize(5120)
                        ->required(),
                ])
                ->action(function (array $data, ImportPathfinders $importer): void {
                    $path = Storage::disk('local')->path($data['file']);
                    $count = $importer->handle($path);
                    Storage::disk('local')->delete($data['file']);

                    Notification::make()->success()->title("{$count} desbravador(es) processado(s)")->send();
                }),
            CreateAction::make(),
        ];
    }
}
