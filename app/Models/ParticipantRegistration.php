<?php

namespace App\Models;

use Database\Factories\ParticipantRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'athlete_name',
    'birth_date',
    'sex',
    'participant_cpf',
    'guardian_name',
    'guardian_cpf',
    'filled_by_legal_representative',
    'phone',
    'email',
    'billing_document',
    'billing_name',
    'billing_address',
    'billing_address_number',
    'billing_province',
    'billing_postal_code',
    'race_modality_id',
    'kit_id',
    'modality',
    'bib_number',
    'result_status',
    'elapsed_time',
    'result_category',
    'overall_rank',
    'sex_rank',
    'category_rank',
    'notes',
    'emergency_contact_name',
    'emergency_contact_phone',
    'health_notes',
    'promotional_opt_in',
    'privacy_policy_accepted_at',
    'privacy_policy_version',
    'privacy_policy_acceptance_ip',
    'privacy_policy_acceptance_user_agent',
    'data_confirmation_accepted_at',
    'data_confirmation_acceptance_ip',
    'data_confirmation_acceptance_user_agent',
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

    protected $hidden = [
        'registration_identity',
    ];

    /**
     * @return BelongsTo<RaceModality, $this>
     */
    public function raceModality(): BelongsTo
    {
        return $this->belongsTo(RaceModality::class);
    }

    /**
     * @return BelongsTo<Kit, $this>
     */
    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
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

    /**
     * @return array<string, string>
     */
    public static function sexOptions(): array
    {
        return [
            'male' => 'Masculino',
            'female' => 'Feminino',
        ];
    }

    public function sexLabel(): string
    {
        return self::sexOptions()[$this->sex] ?? 'Não informado';
    }

    /** @return array<string, string> */
    public static function resultStatusOptions(): array
    {
        return [
            'awaiting' => 'Aguardando resultado',
            'finished' => 'Concluiu a prova',
            'did_not_start' => 'Não largou',
            'did_not_finish' => 'Não concluiu',
            'disqualified' => 'Desclassificado',
        ];
    }

    public function resultStatusLabel(): string
    {
        return self::resultStatusOptions()[$this->result_status] ?? self::resultStatusOptions()['awaiting'];
    }

    public function paymentStatusLabel(): string
    {
        return self::paymentStatusOptions()[$this->payment_status] ?? 'Pendente';
    }

    public function priceFor(Kit $kit): float
    {
        return (float) $kit->price;
    }

    protected static function booted(): void
    {
        static::creating(function (ParticipantRegistration $registration): void {
            $registration->protocol_number = 'AVR-'.Str::ulid();
            $registration->registration_identity = $registration->participant_cpf;
        });

        static::updating(function (ParticipantRegistration $registration): void {
            if ($registration->registration_identity !== null) {
                $registration->registration_identity = $registration->participant_cpf;
            }
        });
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
            'filled_by_legal_representative' => 'boolean',
            'privacy_policy_accepted_at' => 'datetime',
            'data_confirmation_accepted_at' => 'datetime',
            'overall_rank' => 'integer',
            'sex_rank' => 'integer',
            'category_rank' => 'integer',
        ];
    }
}
