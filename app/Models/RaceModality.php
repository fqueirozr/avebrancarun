<?php

namespace App\Models;

use Database\Factories\RaceModalityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'name',
    'type',
    'age_start',
    'age_end',
    'distance',
    'race_date',
    'race_time',
    'google_maps_embed_url',
    'course_information',
    'course_images',
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

    public function ageRangeLabel(): string
    {
        if ($this->age_start !== null && $this->age_end !== null) {
            return "{$this->age_start} a {$this->age_end} anos";
        }

        if ($this->age_start !== null) {
            return "A partir de {$this->age_start} anos";
        }

        if ($this->age_end !== null) {
            return "Até {$this->age_end} anos";
        }

        return 'Todas as idades';
    }

    public function ageReferenceDate(?Carbon $eventDate = null): Carbon
    {
        return $this->race_date ?? $eventDate ?? today();
    }

    public function acceptsBirthDate(Carbon $birthDate, ?Carbon $eventDate = null): bool
    {
        $referenceDate = $this->ageReferenceDate($eventDate);
        $age = $referenceDate->year - $birthDate->year;

        if ($referenceDate->format('md') < $birthDate->format('md')) {
            $age--;
        }

        return ($this->age_start === null || $age >= $this->age_start)
            && ($this->age_end === null || $age <= $this->age_end);
    }

    public function participantLimitHasBeenReached(): bool
    {
        if ($this->max_participants === null) {
            return false;
        }

        return $this->participantRegistrations()
            ->where('payment_status', '!=', 'cancelled')
            ->count() >= $this->max_participants;
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
            'age_start' => 'integer',
            'age_end' => 'integer',
            'race_date' => 'date',
            'course_images' => 'array',
            'max_participants' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
