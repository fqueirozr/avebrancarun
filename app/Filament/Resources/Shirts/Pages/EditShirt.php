<?php

namespace App\Filament\Resources\Shirts\Pages;

use App\Filament\Resources\Shirts\ShirtResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShirt extends EditRecord
{
    protected static string $resource = ShirtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
