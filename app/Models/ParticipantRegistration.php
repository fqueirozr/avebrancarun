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
    'emergency_contact_name',
    'emergency_contact_phone',
    'health_notes',
    'promotional_opt_in',
    'privacy_policy_accepted_at',
    'privacy_policy_version',
    'privacy_policy_acceptance_ip',
    'privacy_policy_acceptance_user_agent',
    'payment_status',
    'payment_gateway',
    'payment_gateway_reference',
    'payment_checkout_url',
])]
class ParticipantRegistration extends Model
{
    /** @use HasFactory<ParticipantRegistrationFactory> */
    use HasFactory;

    public const PrivacyPolicyVersion = '2026-07-08';

    public const SeniorLegalDiscountMinimumAge = 65;

    public const SeniorLegalDiscountRate = 0.5;

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

    public function isEligibleForSeniorLegalDiscount(): bool
    {
        if ($this->birth_date === null) {
            return false;
        }

        return $this->birth_date->age > self::SeniorLegalDiscountMinimumAge;
    }

    public function priceFor(RaceModality $raceModality): float
    {
        $price = (float) $raceModality->price;

        if (! $this->isEligibleForSeniorLegalDiscount()) {
            return $price;
        }

        return round($price * self::SeniorLegalDiscountRate, 2);
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
            'health_notes' => 'encrypted',
            'promotional_opt_in' => 'boolean',
            'privacy_policy_accepted_at' => 'datetime',
        ];
    }
}
