<?php

namespace App\Filament\Widgets;

use App\Support\DashboardMetrics;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShirtSizeOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Camisetas por tamanho';

    protected ?string $description = 'Pedidos não cancelados, incluindo camisetas dos kits e itens avulsos.';

    protected function getStats(): array
    {
        return collect(app(DashboardMetrics::class)->shirtCountsBySize())
            ->map(fn (array $counts, string $size): Stat => Stat::make("Tamanho {$size}", $counts['total'])
                ->description("{$counts['included']} em inscrições + {$counts['standalone']} avulsas"))
            ->values()
            ->all();
    }
}
