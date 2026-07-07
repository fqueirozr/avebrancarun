<?php

namespace App\Filament\Resources\RaceModalities\Pages;

use App\Filament\Resources\RaceModalities\RaceModalityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRaceModality extends EditRecord
{
    protected static string $resource = RaceModalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
