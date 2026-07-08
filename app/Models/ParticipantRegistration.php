<?php

namespace App\Models;

use Database\Factories\ParticipantRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'athlete_name',
    'birth_date',
    'participant_cpf',
    'guardian_name',
    'guardian_cpf',
    'phone',
    'email',
    'billing_document',
    'billing_name',
    'billing_address',
    'billing_address_number',
    'billing_province',
    'billing_postal_code',
    'race_modality_id',
    'modality',
    'notes',
    'payment_status',
    'payment_gateway',
    'payment_gateway_reference',
    'payment_checkout_url',
])]
class ParticipantRegistration extends Model
{
    /** @use HasFactory<ParticipantRegistrationFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<RaceModality, $this>
     */
    public function raceModality(): BelongsTo
    {
        return $this->belongsTo(RaceModality::class);
    }

    /**
     * @return array<string, string>
     */
    public static function paymentStatusOptions(): array
    {
        return [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'cancelled' => 'Cancelado',
        ];
    }

    public function paymentStatusLabel(): string
    {
        return self::paymentStatusOptions()[$this->payment_status] ?? 'Pendente';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }
}
