<?php

namespace App\Filament\Resources\ShirtOrders\Pages;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShirtOrder extends EditRecord
{
    protected static string $resource = ShirtOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
