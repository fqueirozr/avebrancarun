<?php

namespace App\Models;

use Database\Factories\PathfinderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'code', 'is_active'])]
class Pathfinder extends Model
{
    /** @use HasFactory<PathfinderFactory> */
    use HasFactory;

    public function registration(): HasOne
    {
        return $this->hasOne(ParticipantRegistration::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(ParticipantRegistration::class, 'referred_by_pathfinder_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Pathfinder $pathfinder): void {
            if (blank($pathfinder->code)) {
                do {
                    $pathfinder->code = (string) random_int(1000, 9999);
                } while (self::query()->where('code', $pathfinder->code)->exists());
            }
        });
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
