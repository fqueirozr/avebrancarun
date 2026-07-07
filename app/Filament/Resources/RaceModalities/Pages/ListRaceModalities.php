<?php

namespace App\Filament\Resources\RaceModalities\Pages;

use App\Filament\Resources\RaceModalities\RaceModalityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRaceModalities extends ListRecords
{
    protected static string $resource = RaceModalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
