<?php

namespace App\Filament\Widgets;

use App\Support\DashboardMetrics;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Inscrições e arrecadação';

    protected function getStats(): array
    {
        $metrics = app(DashboardMetrics::class);
        $counts = $metrics->registrationCounts();

        return [
            Stat::make('Inscrições pagas', $counts['paid'])->color('success'),
            Stat::make('Em análise', $counts['under_review'])->color('warning'),
            Stat::make('Pendentes', $counts['pending'])->color('gray'),
            Stat::make('Canceladas', $counts['cancelled'])->color('danger'),
            Stat::make('Valor arrecadado', 'R$ '.number_format($metrics->collectedRevenue(), 2, ',', '.'))
                ->description('Inscrições e itens avulsos pagos')
                ->color('success'),
        ];
    }
}
