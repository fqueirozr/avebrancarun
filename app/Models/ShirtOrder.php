<?php

namespace App\Models;

use Database\Factories\ShirtOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shirt_id', 'participant_registration_id', 'customer_name', 'customer_cpf', 'customer_email', 'customer_phone', 'size', 'sizes', 'quantity', 'unit_price', 'total_price', 'payment_status', 'payment_gateway', 'payment_gateway_reference', 'payment_checkout_url', 'pix_receipt_path', 'pix_receipt_submitted_at', 'payment_reminder_sent_at'])]
class ShirtOrder extends Model
{
    /** @use HasFactory<ShirtOrderFactory> */
    use HasFactory;

    protected $hidden = ['customer_cpf'];

    /** @return array<string, string> */
    public static function paymentStatusOptions(): array
    {
        return ParticipantRegistration::paymentStatusOptions();
    }

    public function shirt(): BelongsTo
    {
        return $this->belongsTo(Shirt::class);
    }

    public function participantRegistration(): BelongsTo
    {
        return $this->belongsTo(ParticipantRegistration::class);
    }

    public function sizeSummary(): string
    {
        $sizes = $this->sizes ?: array_fill(0, $this->quantity, $this->size);

        if (count(array_unique($sizes)) === 1) {
            return $sizes[0];
        }

        return collect($sizes)
            ->map(fn (string $size, int $index): string => ($index + 1).": {$size}")
            ->implode('; ');
    }

    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'pix_receipt_submitted_at' => 'datetime',
            'payment_reminder_sent_at' => 'datetime',
        ];
    }
}
