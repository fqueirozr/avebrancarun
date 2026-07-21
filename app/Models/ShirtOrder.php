<?php

namespace App\Models;

use Database\Factories\ShirtOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shirt_id', 'participant_registration_id', 'customer_name', 'customer_email', 'customer_phone', 'size', 'quantity', 'unit_price', 'total_price', 'payment_status'])]
class ShirtOrder extends Model
{
    /** @use HasFactory<ShirtOrderFactory> */
    use HasFactory;

    /** @return array<string, string> */
    public static function paymentStatusOptions(): array
    {
        return ParticipantRegistration::paymentStatusOptions();
    }

    public function shirt(): BelongsTo
    {
        return $this->belongsTo(Shirt::class);
    }

    public function participantRegistration(): BelongsTo
    {
        return $this->belongsTo(ParticipantRegistration::class);
    }

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'total_price' => 'decimal:2'];
    }
}
