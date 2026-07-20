<?php

namespace App\Filament\Resources\ShirtOrders\Pages;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Models\ShirtOrder;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class PrintShirtOrders extends Page
{
    protected static string $resource = ShirtOrderResource::class;

    protected string $view = 'filament.resources.shirt-orders.pages.print-shirt-orders';

    public function getTitle(): string
    {
        return 'Lista de entrega de camisetas avulsas';
    }

    /**
     * @return Collection<int, ShirtOrder>
     */
    public function getShirtOrders(): Collection
    {
        return ShirtOrder::query()
            ->with('shirt:id,name')
            ->orderBy('shirt_id')
            ->orderBy('size')
            ->orderBy('customer_name')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimir')
                ->icon(Heroicon::Printer)
                ->extraAttributes([
                    'onclick' => 'window.print()',
                ]),
            Action::make('back')
                ->label('Voltar')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->color('gray')
                ->url(fn (): string => ShirtOrderResource::getUrl()),
        ];
    }
}
