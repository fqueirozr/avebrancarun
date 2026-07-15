<?php

namespace App\Models;

use Database\Factories\ParticipantRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'athlete_name',
    'shirt_size',
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
    'pathfinder_id',
    'referred_by_pathfinder_id',
    'pathfinder_upgrade_level',
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
    'regulation_accepted_at',
    'regulation_version',
    'regulation_acceptance_ip',
    'regulation_acceptance_user_agent',
    'privacy_policy_accepted_at',
    'privacy_policy_version',
    'privacy_policy_acceptance_ip',
    'privacy_policy_acceptance_user_agent',
    'data_confirmation_accepted_at',
    'data_confirmation_acceptance_ip',
    'data_confirmation_acceptance_user_agent',
    'special_kit_rules_accepted_at',
    'special_kit_rules_version',
    'special_kit_rules_acceptance_ip',
    'special_kit_rules_acceptance_user_agent',
    'payment_status',
    'payment_gateway',
    'payment_gateway_reference',
    'payment_checkout_url',
    'pix_receipt_path',
    'pix_receipt_submitted_at',
])]
class ParticipantRegistration extends Model
{
    /** @use HasFactory<ParticipantRegistrationFactory> */
    use HasFactory;

    public const PrivacyPolicyVersion = '2026-07-10';

    public const SpecialKitRulesVersion = '2026-07-11';

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

    public function pathfinder(): BelongsTo
    {
        return $this->belongsTo(Pathfinder::class);
    }

    public function referredByPathfinder(): BelongsTo
    {
        return $this->belongsTo(Pathfinder::class, 'referred_by_pathfinder_id');
    }

    /**
     * @return array<string, string>
     */
    public static function paymentStatusOptions(): array
    {
        return [
            'pending' => 'Pendente',
            'under_review' => 'Em análise',
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

    /** @return array<string, string> */
    public static function shirtSizeOptions(): array
    {
        return array_combine(
            ['PP', 'P', 'M', 'G', 'GG', 'XGG'],
            ['PP', 'P', 'M', 'G', 'GG', 'XGG'],
        );
    }

    /** @return array<string, string> */
    public static function resultCategoryOptions(): array
    {
        return collect(['6–7', '8–9', '10–11', '12–13', '14–19', '20–29', '30–39', '40–49', '50–59', '60+'])
            ->flatMap(fn (string $ageRange): array => [
                "Masculino {$ageRange}" => "Masculino {$ageRange}",
                "Feminino {$ageRange}" => "Feminino {$ageRange}",
            ])
            ->all();
    }

    public static function resultCategoryFor(string $sex, Carbon $birthDate, Carbon $referenceDate): ?string
    {
        $age = $birthDate->diff($referenceDate)->y;

        if ($age < 6) {
            return null;
        }

        $ageRange = match (true) {
            $age >= 6 && $age <= 7 => '6–7',
            $age <= 9 => '8–9',
            $age <= 11 => '10–11',
            $age <= 13 => '12–13',
            $age <= 19 => '14–19',
            $age <= 29 => '20–29',
            $age <= 39 => '30–39',
            $age <= 49 => '40–49',
            $age <= 59 => '50–59',
            $age >= 60 => '60+',
            default => null,
        };

        $sexLabel = self::sexOptions()[$sex] ?? null;

        if ($ageRange === null || $sexLabel === null) {
            return null;
        }

        return "{$sexLabel} {$ageRange}";
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
            do {
                $protocolNumber = 'AVR-'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (self::query()->where('protocol_number', $protocolNumber)->exists());

            $registration->protocol_number = $protocolNumber;
            $registration->registration_identity = $registration->participant_cpf;
        });

        static::updating(function (ParticipantRegistration $registration): void {
            if ($registration->registration_identity !== null) {
                $registration->registration_identity = $registration->participant_cpf;
            }

            if ($registration->isDirty('kit_id')) {
                $kit = Kit::query()->find($registration->kit_id);

                if ($kit?->type !== Kit::TypeStandard) {
                    $registration->referred_by_pathfinder_id = null;
                }

                if ($kit?->type !== Kit::TypePathfinder) {
                    $registration->pathfinder_id = null;
                    $registration->pathfinder_upgrade_level = 0;
                }
            }
        });

        static::saved(function (ParticipantRegistration $registration): void {
            $pathfinderIds = [
                $registration->getOriginal('referred_by_pathfinder_id'),
                $registration->referred_by_pathfinder_id,
            ];

            if ($registration->wasRecentlyCreated || $registration->wasChanged('pathfinder_id')) {
                $pathfinderIds[] = $registration->getOriginal('pathfinder_id');
                $pathfinderIds[] = $registration->pathfinder_id;
            }

            Pathfinder::query()
                ->whereKey(collect($pathfinderIds)->filter()->unique())
                ->each(fn (Pathfinder $pathfinder) => $pathfinder->recalculateRegistrationUpgrade());
        });

        static::deleted(function (ParticipantRegistration $registration): void {
            if ($registration->referred_by_pathfinder_id === null) {
                return;
            }

            Pathfinder::query()
                ->find($registration->referred_by_pathfinder_id)
                ?->recalculateRegistrationUpgrade();
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
            'filled_by_legal_representative' => 'boolean',
            'regulation_accepted_at' => 'datetime',
            'privacy_policy_accepted_at' => 'datetime',
            'data_confirmation_accepted_at' => 'datetime',
            'special_kit_rules_accepted_at' => 'datetime',
            'overall_rank' => 'integer',
            'sex_rank' => 'integer',
            'category_rank' => 'integer',
            'pathfinder_upgrade_level' => 'integer',
            'pix_receipt_submitted_at' => 'datetime',
        ];
    }
}
