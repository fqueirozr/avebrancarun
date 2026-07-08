<?php

namespace App\Http\Controllers;

use App\Mail\ParticipantRegistrationUpdated;
use App\Models\ParticipantRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AsaasWebhookController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const PAID_EVENTS = [
        'PAYMENT_CONFIRMED',
        'PAYMENT_RECEIVED',
        'PAYMENT_RECEIVED_IN_CASH',
    ];

    public function __invoke(Request $request): Response
    {
        if (! in_array($request->string('event')->toString(), self::PAID_EVENTS, true)) {
            return response()->noContent();
        }

        $registration = $this->findRegistration($request);

        if (! $registration) {
            Log::warning('Asaas payment webhook could not be matched to a participant registration.', [
                'event' => $request->input('event'),
                'payment_id' => $request->input('payment.id'),
                'external_reference' => $request->input('payment.externalReference'),
            ]);

            return response()->noContent();
        }

        if ($registration->payment_status !== 'paid') {
            $registration->update([
                'payment_status' => 'paid',
            ]);

            Mail::to($registration->email)->send(new ParticipantRegistrationUpdated($registration));
        }

        return response()->noContent();
    }

    private function findRegistration(Request $request): ?ParticipantRegistration
    {
        $registrationId = $this->registrationIdFromExternalReference(
            $request->string('payment.externalReference')->toString()
        );

        if ($registrationId !== null) {
            return ParticipantRegistration::query()->find($registrationId);
        }

        $gatewayReferences = collect([
            $request->input('checkout.id'),
            $request->input('checkoutSession.id'),
            $request->input('payment.checkoutSession'),
            $request->input('payment.checkout.id'),
            $request->input('payment.checkoutSession.id'),
        ])
            ->filter(fn (mixed $reference): bool => filled($reference))
            ->map(fn (mixed $reference): string => (string) $reference)
            ->unique()
            ->values();

        if ($gatewayReferences->isEmpty()) {
            return null;
        }

        return ParticipantRegistration::query()
            ->where('payment_gateway', 'asaas')
            ->whereIn('payment_gateway_reference', $gatewayReferences)
            ->first();
    }

    private function registrationIdFromExternalReference(string $externalReference): ?int
    {
        if (! str($externalReference)->startsWith('participant-registration:')) {
            return null;
        }

        $registrationId = str($externalReference)
            ->after('participant-registration:')
            ->toString();

        return filter_var($registrationId, FILTER_VALIDATE_INT) ?: null;
    }
}
