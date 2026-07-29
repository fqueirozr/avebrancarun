<?php

namespace App\Console\Commands;

use App\Jobs\SendPendingPaymentReminder;
use App\Models\ParticipantRegistration;
use App\Models\ShirtOrder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('payments:send-pending-reminders')]
#[Description('Envia lembretes de pagamentos que permanecem pendentes por 60 minutos')]
class SendPendingPaymentReminders extends Command
{
    public function handle(): int
    {
        $createdBefore = now()->subHour();

        ParticipantRegistration::query()
            ->where('payment_status', 'pending')
            ->whereNull('payment_reminder_sent_at')
            ->where('created_at', '<=', $createdBefore)
            ->eachById(function (ParticipantRegistration $registration): void {
                SendPendingPaymentReminder::dispatchSync('registration', $registration->getKey());
            });

        ShirtOrder::query()
            ->whereNull('participant_registration_id')
            ->where('payment_status', 'pending')
            ->whereNull('payment_reminder_sent_at')
            ->where('created_at', '<=', $createdBefore)
            ->eachById(function (ShirtOrder $shirtOrder): void {
                SendPendingPaymentReminder::dispatchSync('shirt-order', $shirtOrder->getKey());
            });

        return self::SUCCESS;
    }
}
