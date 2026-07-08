<?php

namespace App\Filament\Resources\ParticipantRegistrations\Pages;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Mail\ParticipantRegistrationUpdated;
use App\Models\RaceModality;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

class EditParticipantRegistration extends EditRecord
{
    protected static string $resource = ParticipantRegistrationResource::class;

    protected ?string $previousPaymentStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancelar inscrição')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancelar inscrição')
                ->modalDescription('A inscrição será marcada como cancelada e o participante receberá um e-mail de atualização.')
                ->visible(fn (): bool => $this->record->payment_status !== 'cancelled')
                ->action(function (): void {
                    $this->record->forceFill([
                        'payment_status' => 'cancelled',
                    ])->save();

                    Mail::to($this->record->email)->send(new ParticipantRegistrationUpdated($this->record));

                    Notification::make()
                        ->success()
                        ->title('Inscrição cancelada')
                        ->body('O participante recebeu um e-mail de atualização.')
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousPaymentStatus = $this->record->payment_status;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['race_modality_id'])) {
            $data['modality'] = RaceModality::query()
                ->findOrFail($data['race_modality_id'])
                ->displayName();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->previousPaymentStatus === $this->record->payment_status) {
            return;
        }

        Mail::to($this->record->email)->send(new ParticipantRegistrationUpdated($this->record));
    }
}
