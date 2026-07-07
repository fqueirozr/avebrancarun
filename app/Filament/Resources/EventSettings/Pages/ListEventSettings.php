<?php

namespace App\Filament\Resources\EventSettings\Pages;

use App\Filament\Resources\EventSettings\EventSettingResource;
use App\Models\EventSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventSettings extends ListRecords
{
    protected static string $resource = EventSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => EventSetting::query()->doesntExist()),
        ];
    }
}
