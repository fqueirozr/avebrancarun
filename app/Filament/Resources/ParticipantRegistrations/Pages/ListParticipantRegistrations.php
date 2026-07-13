<?php

namespace App\Filament\Resources\ParticipantRegistrations\Pages;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListParticipantRegistrations extends ListRecords
{
    protected static string $resource = ParticipantRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Lista de entrega de kits')
                ->icon(Heroicon::Printer)
                ->url(fn (): string => ParticipantRegistrationResource::getUrl('print'))
                ->openUrlInNewTab(),
        ];
    }
}
