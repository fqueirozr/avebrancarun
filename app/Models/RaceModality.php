<?php

namespace App\Models;

use Database\Factories\RaceModalityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'type',
    'age_range',
    'distance',
    'google_maps_embed_url',
    'price',
    'max_participants',
    'is_active',
    'sort_order',
    'description',
])]
class RaceModality extends Model
{
    /** @use HasFactory<RaceModalityFactory> */
    use HasFactory;

    /**
     * @return HasMany<ParticipantRegistration, $this>
     */
    public function participantRegistrations(): HasMany
    {
        return $this->hasMany(ParticipantRegistration::class);
    }

    public function displayName(): string
    {
        if ($this->distance === null || $this->distance === '') {
            return $this->name;
        }

        return "{$this->name} - {$this->distance}";
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
            ->mapWithKeys(fn (RaceModality $modality): array => [
                $modality->id => $modality->displayName(),
            ])
            ->all();
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
