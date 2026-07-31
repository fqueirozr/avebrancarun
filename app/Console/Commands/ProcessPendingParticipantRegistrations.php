<?php

namespace App\Console\Commands;

use App\Mail\PendingRegistrationPaymentReminder;
use App\Models\ParticipantRegistration;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

#[Signature('registrations:process-pending-payments')]
#[Description('Envia lembretes e cancela inscrições com pagamento pendente fora do prazo')]
class ProcessPendingParticipantRegistrations extends Command
{
    public function handle(): int
    {
        $cancelledCount = 0;
        $reminderCount = 0;

        ParticipantRegistration::query()
            ->where('payment_status', 'pending')
            ->where('created_at', '<=', now()->subDays(7))
            ->select('id')
            ->chunkById(100, function ($registrations) use (&$cancelledCount): void {
                foreach ($registrations as $registration) {
                    $wasCancelled = DB::transaction(function () use ($registration): bool {
                        $pendingRegistration = ParticipantRegistration::query()
                            ->lockForUpdate()
                            ->find($registration->id);

                        if (
                            $pendingRegistration === null
                            || $pendingRegistration->payment_status !== 'pending'
                            || $pendingRegistration->created_at->isAfter(now()->subDays(7))
                        ) {
                            return false;
                        }

                        $pendingRegistration->update([
                            'payment_status' => 'cancelled',
                            'cancellation_source' => ParticipantRegistration::CancellationSourceAutomatic,
                        ]);

                        return true;
                    });

                    if ($wasCancelled) {
                        $cancelledCount++;
                    }
                }
            });

        ParticipantRegistration::query()
            ->where('payment_status', 'pending')
            ->whereNull('payment_reminder_sent_at')
            ->where('created_at', '<=', now()->subDays(4))
            ->where('created_at', '>', now()->subDays(7))
            ->select('id')
            ->chunkById(100, function ($registrations) use (&$reminderCount): void {
                foreach ($registrations as $registration) {
                    $pendingRegistration = DB::transaction(function () use ($registration): ?ParticipantRegistration {
                        $lockedRegistration = ParticipantRegistration::query()
                            ->lockForUpdate()
                            ->find($registration->id);

                        if (
                            $lockedRegistration === null
                            || $lockedRegistration->payment_status !== 'pending'
                            || $lockedRegistration->payment_reminder_sent_at !== null
                            || $lockedRegistration->created_at->isAfter(now()->subDays(4))
                            || $lockedRegistration->created_at->lte(now()->subDays(7))
                        ) {
                            return null;
                        }

                        $lockedRegistration->payment_reminder_sent_at = now();
                        $lockedRegistration->saveQuietly();

                        return $lockedRegistration;
                    });

                    if ($pendingRegistration === null) {
                        continue;
                    }

                    $paymentUrl = URL::temporarySignedRoute(
                        'registration.payment.show',
                        now()->addDays(4),
                        ['registration' => $pendingRegistration],
                    );

                    try {
                        Mail::to($pendingRegistration->email)->send(
                            new PendingRegistrationPaymentReminder($pendingRegistration, $paymentUrl),
                        );
                    } catch (Throwable $exception) {
                        $pendingRegistration->updateQuietly(['payment_reminder_sent_at' => null]);

                        throw $exception;
                    }

                    $reminderCount++;
                }
            });

        $this->components->info(
            "{$reminderCount} lembrete(s) enviado(s) e {$cancelledCount} inscrição(ões) cancelada(s).",
        );

        return self::SUCCESS;
    }
}
