<?php

namespace App\Filament\Resources\Shirts\Pages;

use App\Filament\Resources\Shirts\ShirtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShirts extends ListRecords
{
    protected static string $resource = ShirtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
