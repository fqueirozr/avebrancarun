<?php

namespace App\Observers;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Models\ShirtOrder;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ShirtOrderObserver implements ShouldHandleEventsAfterCommit
{
    public function created(ShirtOrder $shirtOrder): void
    {
        if ($shirtOrder->participant_registration_id !== null) {
            return;
        }

        $notification = Notification::make()
            ->title('Novo pedido de camiseta avulsa')
            ->body("Um novo pedido de camiseta foi realizado por {$shirtOrder->customer_name}.")
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
}
