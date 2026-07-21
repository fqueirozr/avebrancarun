<?php

namespace App\Actions;

use App\Models\ParticipantRegistration;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use Illuminate\Validation\ValidationException;

class CreateShirtOrder
{
    /** @param array{customer_name:string,customer_email:string,customer_phone:string,size:string,quantity:int} $data */
    public function handle(Shirt $shirt, array $data, ?ParticipantRegistration $registration = null): ShirtOrder
    {
        $shirt = Shirt::query()->lockForUpdate()->findOrFail($shirt->id);

        if (! $shirt->is_active || ($shirt->stock_quantity !== null && $shirt->stock_quantity < $data['quantity'])) {
            throw ValidationException::withMessages(['shirt_id' => 'A camiseta selecionada não possui estoque suficiente.']);
        }

        if ($shirt->stock_quantity !== null) {
            $shirt->decrement('stock_quantity', $data['quantity']);
        }

        return $shirt->orders()->create([
            ...$data,
            'participant_registration_id' => $registration?->id,
            'unit_price' => $shirt->price,
            'total_price' => (float) $shirt->price * $data['quantity'],
            'payment_status' => $registration?->payment_status ?? 'pending',
        ]);
    }
}
