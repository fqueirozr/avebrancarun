<?php

namespace App\Models;

use Database\Factories\PathfinderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'cpf', 'is_active'])]
class Pathfinder extends Model
{
    /** @use HasFactory<PathfinderFactory> */
    use HasFactory;

    public function registration(): HasOne
    {
        return $this->hasOne(ParticipantRegistration::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
