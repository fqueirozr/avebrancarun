<?php

namespace App\Filament\Resources\MailSettings\Pages;

use App\Filament\Resources\MailSettings\MailSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMailSetting extends EditRecord
{
    protected static string $resource = MailSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
