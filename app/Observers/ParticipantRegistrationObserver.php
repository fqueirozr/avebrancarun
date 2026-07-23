<?php

namespace App\Observers;

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Mail\ParticipantRegistrationUpdated;
use App\Models\ParticipantRegistration;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Mail;

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

    public function updated(ParticipantRegistration $registration): void
    {
        if ($registration->wasChanged('updated_at') && count($registration->getChanges()) === 1) {
            return;
        }

        Mail::to($registration->email)->send(new ParticipantRegistrationUpdated(
            $registration,
            $this->updateTitle($registration),
        ));
    }

    private function updateTitle(ParticipantRegistration $registration): string
    {
        if ($registration->wasChanged('payment_status')) {
            return match ($registration->payment_status) {
                'paid' => 'Inscrição confirmada',
                'cancelled' => 'Inscrição cancelada',
                'under_review' => 'Inscrição em análise',
                default => 'Pagamento da inscrição atualizado',
            };
        }

        return 'Dados da inscrição atualizados';
    }
}
