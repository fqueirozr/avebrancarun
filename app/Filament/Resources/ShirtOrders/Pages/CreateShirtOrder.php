<?php

namespace App\Filament\Resources\ShirtOrders\Pages;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShirtOrder extends CreateRecord
{
    protected static string $resource = ShirtOrderResource::class;
}
