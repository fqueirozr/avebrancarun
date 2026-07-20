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

    public function quantityLimitHasBeenReached(): bool
    {
        if ($this->max_quantity === null) {
            return false;
        }

        return $this->participantRegistrations()
            ->where('payment_status', '!=', 'cancelled')
            ->count() >= $this->max_quantity;
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
            'is_active' => 'boolean',
        ];
    }
}
