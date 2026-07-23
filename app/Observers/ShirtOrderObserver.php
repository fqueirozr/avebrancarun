<?php

namespace App\Observers;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Mail\ShirtOrderUpdated;
use App\Models\ShirtOrder;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Mail;

class ShirtOrderObserver implements ShouldHandleEventsAfterCommit
{
    public function created(ShirtOrder $shirtOrder): void
    {
        if ($shirtOrder->participant_registration_id !== null) {
            return;
        }

        $notification = Notification::make()
            ->title('Novo pedido de item avulso')
            ->body("Um novo pedido de item avulso foi realizado por {$shirtOrder->customer_name}.")
            ->icon('heroicon-o-shopping-bag')
            ->success()
            ->actions([
                Action::make('view')
                    ->label('Ver pedido')
                    ->url(ShirtOrderResource::getUrl('edit', ['record' => $shirtOrder]))
                    ->markAsRead(),
            ]);

        User::query()->eachById(
            fn (User $user) => $notification->sendToDatabase($user),
        );
    }

    public function updated(ShirtOrder $shirtOrder): void
    {
        if ($shirtOrder->wasChanged('updated_at') && count($shirtOrder->getChanges()) === 1) {
            return;
        }

        $title = $shirtOrder->wasChanged('payment_status')
            ? match ($shirtOrder->payment_status) {
                'paid' => 'Item avulso confirmado',
                'cancelled' => 'Item avulso cancelado',
                'under_review' => 'Item avulso em análise',
                default => 'Pagamento do item avulso atualizado',
            }
        : 'Item avulso atualizado';

        Mail::to($shirtOrder->customer_email)->send(new ShirtOrderUpdated(
            $shirtOrder->loadMissing('shirt'),
            $title,
        ));
    }
}
