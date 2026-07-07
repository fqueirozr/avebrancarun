<?php

namespace App\Filament\Resources\ParticipantRegistrations\Pages;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Models\RaceModality;
use Filament\Resources\Pages\CreateRecord;

class CreateParticipantRegistration extends CreateRecord
{
    protected static string $resource = ParticipantRegistrationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (filled($data['race_modality_id'])) {
            $data['modality'] = RaceModality::query()
                ->findOrFail($data['race_modality_id'])
                ->displayName();
        }

        return $data;
    }
}
