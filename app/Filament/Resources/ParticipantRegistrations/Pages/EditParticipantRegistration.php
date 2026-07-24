<?php

namespace App\Filament\Resources\ParticipantRegistrations\Pages;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Models\RaceModality;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditParticipantRegistration extends EditRecord
{
    protected static string $resource = ParticipantRegistrationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ([
            'participant_cpf',
            'guardian_cpf',
            'phone',
            'email',
            'billing_document',
            'billing_name',
            'billing_address',
            'billing_address_number',
            'billing_province',
            'billing_postal_code',
            'emergency_contact_name',
            'emergency_contact_phone',
            'payment_gateway',
            'payment_gateway_reference',
            'payment_checkout_url',
            'pix_receipt_path',
        ] as $attribute) {
            $data[$attribute] = $this->record->getAttribute($attribute);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancelar inscrição')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancelar inscrição')
                ->modalDescription('A inscrição será marcada como cancelada e o atleta receberá um e-mail de atualização.')
                ->visible(fn (): bool => $this->record->payment_status !== 'cancelled')
                ->action(function (): void {
                    $this->record->forceFill([
                        'payment_status' => 'cancelled',
                    ])->save();

                    Notification::make()
                        ->success()
                        ->title('Inscrição cancelada')
                        ->body('O atleta recebeu um e-mail de atualização.')
                        ->send();
                }),
            DeleteAction::make(),
        ];
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
}
