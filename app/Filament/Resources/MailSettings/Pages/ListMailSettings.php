<?php

namespace App\Filament\Resources\MailSettings\Pages;

use App\Filament\Resources\MailSettings\MailSettingResource;
use App\Models\MailSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMailSettings extends ListRecords
{
    protected static string $resource = MailSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => MailSetting::query()->doesntExist()),
        ];
    }
}
