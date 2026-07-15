<?php

namespace App\Models;

use Database\Factories\KitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'photo_path',
    'description',
    'price',
    'type',
    'rules',
    'max_quantity',
    'has_shirt',
    'upgrade_1_referrals',
    'upgrade_1_contents',
    'upgrade_2_referrals',
    'upgrade_2_contents',
    'upgrade_3_referrals',
    'upgrade_3_contents',
    'is_active',
    'sort_order',
])]
class Kit extends Model
{
    /** @use HasFactory<KitFactory> */
    use HasFactory;

    public const TypeStandard = 'standard';

    public const TypePcd60 = 'pcd_60';

    public const TypeSocial = 'social';

    public const TypePathfinder = 'pathfinder';

    protected $attributes = [
        'type' => self::TypeStandard,
        'has_shirt' => true,
    ];

    public static function typeOptions(): array
    {
        return [self::TypeStandard => 'Normal', self::TypePcd60 => 'PCD / 60+', self::TypeSocial => 'Kit Social', self::TypePathfinder => 'Desbravador'];
    }

    public function requiresRulesAcknowledgement(): bool
    {
        return $this->type !== self::TypeStandard;
    }

    public function allowsReferralCode(): bool
    {
        return in_array($this->type, [self::TypeStandard, self::TypePathfinder], true);
    }

    public function quantityLimitHasBeenReached(): bool
    {
        if ($this->max_quantity === null) {
            return false;
        }

        return $this->participantRegistrations()
            ->where('payment_status', '!=', 'cancelled')
            ->count() >= $this->max_quantity;
    }

    public function upgradeLevelFor(int $referrals): int
    {
        if ($this->type !== self::TypePathfinder) {
            return 0;
        }

        return collect([$this->upgrade_1_referrals, $this->upgrade_2_referrals, $this->upgrade_3_referrals])
            ->filter(fn ($threshold): bool => $threshold !== null && $referrals >= (int) $threshold)
            ->count();
    }

    /**
     * @return array<int, string>
     */
    public function upgradeContentsThroughLevel(int $level): array
    {
        $maximumLevel = min(max($level, 0), 3);

        if ($maximumLevel === 0) {
            return [];
        }

        return collect(range(1, $maximumLevel))
            ->map(fn (int $upgradeLevel): ?string => $this->{"upgrade_{$upgradeLevel}_contents"})
            ->filter(fn (?string $contents): bool => filled($contents))
            ->values()
            ->all();
    }

    /**
     * @return HasMany<ParticipantRegistration, $this>
     */
    public function participantRegistrations(): HasMany
    {
        return $this->hasMany(ParticipantRegistration::class);
    }

    /**
     * @return array<int, string>
     */
    public static function activeOptions(): array
    {
        return self::options(activeOnly: true);
    }

    /**
     * @return array<int, string>
     */
    public static function options(bool $activeOnly = false): array
    {
        return self::query()
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Kit $kit): array => [
                $kit->id => $kit->displayName(),
            ])
            ->all();
    }

    public function displayName(): string
    {
        return "{$this->name} - R$ ".number_format((float) $this->price, 2, ',', '.');
    }

    protected static function booted(): void
    {
        static::saved(function (Kit $kit): void {
            if (! $kit->wasChanged([
                'type',
                'upgrade_1_referrals',
                'upgrade_2_referrals',
                'upgrade_3_referrals',
            ])) {
                return;
            }

            $kit->participantRegistrations()
                ->whereNotNull('pathfinder_id')
                ->with('pathfinder')
                ->each(fn (ParticipantRegistration $registration) => $registration->pathfinder?->recalculateRegistrationUpgrade());
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
            'price' => 'decimal:2',
            'max_quantity' => 'integer',
            'has_shirt' => 'boolean',
            'upgrade_1_referrals' => 'integer',
            'upgrade_2_referrals' => 'integer',
            'upgrade_3_referrals' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
