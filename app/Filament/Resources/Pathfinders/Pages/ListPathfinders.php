<?php

namespace App\Filament\Resources\Pathfinders\Pages;

use App\Filament\Resources\Pathfinders\PathfinderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPathfinders extends ListRecords
{
    protected static string $resource = PathfinderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
