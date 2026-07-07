<?php

namespace App\Models;

use Database\Factories\EventSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'event_date',
    'event_location',
    'kit_information',
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
}
