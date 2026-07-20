<?php

namespace App\Filament\Resources\MailSettings\Pages;

use App\Filament\Resources\MailSettings\MailSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMailSetting extends CreateRecord
{
    protected static string $resource = MailSettingResource::class;
}
