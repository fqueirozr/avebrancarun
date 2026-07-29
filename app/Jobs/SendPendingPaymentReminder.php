<?php

namespace App\Jobs;

use App\Mail\PendingRegistrationPaymentReminder;
use App\Mail\PendingShirtOrderPaymentReminder;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use App\Models\ShirtOrder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class SendPendingPaymentReminder implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900];

    public int $uniqueFor = 7200;

    public function __construct(
        public string $paymentType,
        public int $paymentId,
    ) {}

    public static function forRegistration(ParticipantRegistration $registration): self
    {
        return new self('registration', $registration->getKey());
    }

    public static function forShirtOrder(ShirtOrder $shirtOrder): self
    {
        return new self('shirt-order', $shirtOrder->getKey());
    }

    public function handle(): void
    {
        if ($this->paymentType === 'registration') {
            $this->remindRegistration();

            return;
        }

        if ($this->paymentType === 'shirt-order') {
            $this->remindShirtOrder();
        }
    }

    public function uniqueId(): string
    {
        return "{$this->paymentType}:{$this->paymentId}";
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Falha ao enviar lembrete de pagamento pendente.', [
            'payment_type' => $this->paymentType,
            'payment_id' => $this->paymentId,
            'error' => $exception?->getMessage(),
        ]);
    }

    private function remindRegistration(): void
    {
        $registration = ParticipantRegistration::query()
            ->with(['kit', 'shirtOrders'])
            ->find($this->paymentId);

        if (
            $registration === null
            || $registration->payment_status !== 'pending'
            || $registration->payment_reminder_sent_at !== null
            || $registration->kit === null
            || $registration->priceFor($registration->kit) <= 0
        ) {
            return;
        }

        $athleteUrl = URL::temporarySignedRoute(
            'athlete.show',
            now()->addDays(7),
            ['registration' => $registration],
        );

        Mail::to($registration->email)->send(
            new PendingRegistrationPaymentReminder($registration, $athleteUrl),
        );

        $registration->updateQuietly(['payment_reminder_sent_at' => now()]);
    }

    private function remindShirtOrder(): void
    {
        $shirtOrder = ShirtOrder::query()
            ->with('shirt')
            ->find($this->paymentId);

        if (
            $shirtOrder === null
            || $shirtOrder->participant_registration_id !== null
            || $shirtOrder->payment_status !== 'pending'
            || $shirtOrder->payment_reminder_sent_at !== null
            || (float) $shirtOrder->total_price <= 0
        ) {
            return;
        }

        $paymentUrl = $this->shirtOrderPaymentUrl($shirtOrder);

        if ($paymentUrl === null) {
            return;
        }

        Mail::to($shirtOrder->customer_email)->send(
            new PendingShirtOrderPaymentReminder($shirtOrder, $paymentUrl),
        );

        $shirtOrder->updateQuietly(['payment_reminder_sent_at' => now()]);
    }

    private function shirtOrderPaymentUrl(ShirtOrder $shirtOrder): ?string
    {
        if (filled($shirtOrder->payment_checkout_url)) {
            return $shirtOrder->payment_checkout_url;
        }

        if (! PaymentGatewaySetting::current()->hasManualPix()) {
            return null;
        }

        return URL::temporarySignedRoute(
            'store.pix.show',
            now()->addDays(7),
            ['shirtOrder' => $shirtOrder],
        );
    }
}
