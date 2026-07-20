<?php

namespace App\Filament\Resources\ShirtOrders\Pages;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListShirtOrders extends ListRecords
{
    protected static string $resource = ShirtOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Lista de entrega')
                ->icon(Heroicon::Printer)
                ->url(fn (): string => ShirtOrderResource::getUrl('print'))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
