<?php

namespace App\Filament\Resources\ParticipantRegistrations\Pages;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Models\ParticipantRegistration;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class PrintParticipantRegistrations extends Page
{
    protected static string $resource = ParticipantRegistrationResource::class;

    protected string $view = 'filament.resources.participant-registrations.pages.print-participant-registrations';

    public function getTitle(): string
    {
        return 'Lista de entrega de kits';
    }

    /**
     * @return Collection<int, ParticipantRegistration>
     */
    public function getRegistrations(): Collection
    {
        return ParticipantRegistration::query()
            ->with('kit:id,name,type,upgrade_1_contents,upgrade_2_contents,upgrade_3_contents')
            ->where('payment_status', 'paid')
            ->orderBy('kit_id')
            ->orderBy('modality')
            ->orderBy('athlete_name')
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
                ->url(fn (): string => ParticipantRegistrationResource::getUrl()),
        ];
    }
}
