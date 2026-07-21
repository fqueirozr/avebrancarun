<?php

namespace App\Observers;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Models\ParticipantRegistration;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ParticipantRegistrationObserver implements ShouldHandleEventsAfterCommit
{
    public function created(ParticipantRegistration $registration): void
    {
        $notification = Notification::make()
            ->title('Nova inscrição recebida')
            ->body("Uma nova inscrição foi realizada por {$registration->athlete_name}.")
            ->icon('heroicon-o-clipboard-document-check')
            ->success()
            ->actions([
                Action::make('view')
                    ->label('Ver inscrição')
                    ->url(ParticipantRegistrationResource::getUrl('edit', ['record' => $registration]))
                    ->markAsRead(),
            ]);

        User::query()->eachById(
            fn (User $user) => $notification->sendToDatabase($user),
        );
    }
}
