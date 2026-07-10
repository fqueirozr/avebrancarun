<?php

namespace App\Models;

use Database\Factories\EventSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'event_date',
    'event_location',
    'registration_deadline',
    'max_registrations',
    'contact_email',
    'contact_phone',
    'contact_whatsapp',
    'general_information',
    'kit_information',
    'baggage_storage_information',
    'start_groups_information',
    'timing_information',
    'special_registrations_information',
    'regulation',
])]
class EventSetting extends Model
{
    /** @use HasFactory<EventSettingFactory> */
    use HasFactory;

    public static function current(): self
    {
        return self::query()->first() ?? new self;
    }

    public function registrationDeadlineHasPassed(): bool
    {
        return $this->registration_deadline?->isPast() ?? false;
    }

    public function registrationLimitHasBeenReached(): bool
    {
        if ($this->max_registrations === null) {
            return false;
        }

        return ParticipantRegistration::query()
            ->where('payment_status', '!=', 'cancelled')
            ->count() >= $this->max_registrations;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registration_deadline' => 'datetime',
            'max_registrations' => 'integer',
        ];
    }
}
