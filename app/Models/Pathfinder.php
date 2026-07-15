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

    public function upgradeLevel(): int
    {
        $referralsCount = (int) ($this->getAttribute('referrals_count') ?? $this->referrals()->count());

        return $this->upgradeKit()?->upgradeLevelFor($referralsCount) ?? 0;
    }

    /** @return array<int, string> */
    public function upgradeContents(): array
    {
        return $this->upgradeKit()?->upgradeContentsThroughLevel($this->upgradeLevel()) ?? [];
    }

    public function recalculateRegistrationUpgrade(): void
    {
        $registration = $this->registration()->with('kit')->first();

        if ($registration === null) {
            return;
        }

        $registration->update([
            'pathfinder_upgrade_level' => $registration->kit?->upgradeLevelFor($this->referrals()->count()) ?? 0,
        ]);
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

    private function upgradeKit(): ?Kit
    {
        return $this->registration?->kit ?? once(fn (): ?Kit => Kit::query()
            ->where('type', Kit::TypePathfinder)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first());
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
