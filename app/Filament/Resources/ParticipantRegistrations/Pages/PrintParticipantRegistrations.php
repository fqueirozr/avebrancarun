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
        return 'Imprimir inscricoes';
    }

    /**
     * @return Collection<int, ParticipantRegistration>
     */
    public function getRegistrations(): Collection
    {
        return ParticipantRegistration::query()
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
