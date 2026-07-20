<?php

namespace App\Models;

use Database\Factories\ShirtFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'photo_path', 'price', 'stock_quantity', 'is_active'])]
class Shirt extends Model
{
    /** @use HasFactory<ShirtFactory> */
    use HasFactory;

    public function orders(): HasMany
    {
        return $this->hasMany(ShirtOrder::class);
    }

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'stock_quantity' => 'integer', 'is_active' => 'boolean'];
    }
}
