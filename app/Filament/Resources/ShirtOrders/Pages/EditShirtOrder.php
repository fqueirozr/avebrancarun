<?php

namespace App\Filament\Resources\ShirtOrders\Pages;

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Mail\ShirtOrderUpdated;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditShirtOrder extends EditRecord
{
    protected static string $resource = ShirtOrderResource::class;

    protected ?string $previousPaymentStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousPaymentStatus = $this->record->payment_status;
    }

    protected function afterSave(): void
    {
        if ($this->record->participant_registration_id !== null
            || $this->previousPaymentStatus === $this->record->payment_status) {
            return;
        }

        Mail::to($this->record->customer_email)->send(new ShirtOrderUpdated($this->record->loadMissing('shirt')));
    }
}
