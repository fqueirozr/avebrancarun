<?php

namespace App\Actions;

use App\Models\ParticipantRegistration;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use Illuminate\Validation\ValidationException;

class CreateShirtOrder
{
    /** @param array{customer_name:string,customer_cpf?:string,customer_email:string,customer_phone:string,size?:string,sizes?:array<int, string>,quantity:int} $data */
    public function handle(Shirt $shirt, array $data, ?ParticipantRegistration $registration = null): ShirtOrder
    {
        $shirt = Shirt::query()->lockForUpdate()->findOrFail($shirt->id);
        $sizes = $data['sizes'] ?? array_fill(0, $data['quantity'], $data['size']);

        if (! $shirt->is_active || ($shirt->stock_quantity !== null && $shirt->stock_quantity < $data['quantity'])) {
            throw ValidationException::withMessages(['shirt_id' => 'O item selecionado não possui estoque suficiente.']);
        }

        if ($shirt->stock_quantity !== null) {
            $shirt->decrement('stock_quantity', $data['quantity']);
        }

        $baseUnitPrice = $registration === null ? (float) $shirt->price : $shirt->priceForRegistration();
        $totalPrice = collect($sizes)
            ->sum(fn (string $size): float => $baseUnitPrice + $shirt->surchargeForSize($size));

        return $shirt->orders()->create([
            ...$data,
            'size' => $sizes[0],
            'sizes' => $sizes,
            'participant_registration_id' => $registration?->id,
            'unit_price' => $baseUnitPrice,
            'total_price' => $totalPrice,
            'payment_status' => $registration?->payment_status ?? 'pending',
        ]);
    }
}
