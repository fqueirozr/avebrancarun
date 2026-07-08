<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Pages;

use App\Filament\Resources\PaymentGatewaySettings\PaymentGatewaySettingResource;
use Filament\Resources\Pages\EditRecord;

class EditPaymentGatewaySetting extends EditRecord
{
    protected static string $resource = PaymentGatewaySettingResource::class;
}
