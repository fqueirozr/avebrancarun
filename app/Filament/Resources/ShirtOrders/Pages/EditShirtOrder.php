<?php

namespace App\Filament\Resources\ShirtOrders\Pages;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShirtOrder extends EditRecord
{
    protected static string $resource = ShirtOrderResource::class;

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['sizes'] = array_values($data['sizes']);
        $data['size'] = $data['sizes'][0];

        if (
            blank($this->record->sizes)
            && $data['sizes'] === array_fill(0, $this->record->quantity, $this->record->size)
        ) {
            unset($data['sizes']);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
