<?php

namespace App\Filament\Resources\Pathfinders\Pages;

use App\Filament\Resources\Pathfinders\PathfinderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPathfinder extends EditRecord
{
    protected static string $resource = PathfinderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
