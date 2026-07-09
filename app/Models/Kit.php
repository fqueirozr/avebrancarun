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
    'is_active',
    'sort_order',
])]
class Kit extends Model
{
    /** @use HasFactory<KitFactory> */
    use HasFactory;

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
            'is_active' => 'boolean',
        ];
    }
}
