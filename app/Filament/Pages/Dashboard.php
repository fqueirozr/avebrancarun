<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RegistrationOverview;
use App\Filament\Widgets\ShirtSizeOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use UnitEnum;

class Dashboard extends BaseDashboard
{
    protected static string|UnitEnum|null $navigationGroup = 'Geral';

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            RegistrationOverview::class,
            ShirtSizeOverview::class,
        ];
    }
}
