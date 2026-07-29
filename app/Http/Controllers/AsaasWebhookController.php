<?php

namespace App\Http\Controllers;

use App\Models\ParticipantRegistration;
use App\Models\ShirtOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
        $configuredToken = (string) config('payments.asaas.webhook_token');
        $receivedToken = (string) $request->header('asaas-access-token');

        if ($configuredToken === '' || $receivedToken === '' || ! hash_equals($configuredToken, $receivedToken)) {
            return response()->noContent(Response::HTTP_UNAUTHORIZED);
        }

        if (! in_array($request->string('event')->toString(), self::PAID_EVENTS, true)) {
            return response()->noContent();
        }

        $payable = $this->findRegistration($request) ?? $this->findShirtOrder($request);

        if (! $payable) {
            Log::warning('Asaas payment webhook could not be matched to an order.', [
                'event' => $request->input('event'),
                'payment_id' => $request->input('payment.id'),
                'external_reference' => $request->input('payment.externalReference'),
            ]);

            return response()->noContent();
        }

        if ($payable->payment_status !== 'paid') {
            $payable->update([
                'payment_status' => 'paid',
            ]);
        }

        return response()->noContent();
    }

    private function findRegistration(Request $request): ?ParticipantRegistration
    {
        $registrationId = $this->registrationIdFromExternalReference(
            $request->string('payment.externalReference')->toString()
        );

        if ($registrationId !== null) {
            return ParticipantRegistration::query()
                ->where('payment_gateway', 'asaas')
                ->find($registrationId);
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

    private function findShirtOrder(Request $request): ?ShirtOrder
    {
        $externalReference = $request->string('payment.externalReference')->toString();

        if (str($externalReference)->startsWith('shirt-order:')) {
            $shirtOrderId = filter_var(
                str($externalReference)->after('shirt-order:')->toString(),
                FILTER_VALIDATE_INT,
            );

            if ($shirtOrderId !== false) {
                return ShirtOrder::query()
                    ->where('payment_gateway', 'asaas')
                    ->find($shirtOrderId);
            }
        }

        $gatewayReferences = collect([
            $request->input('checkout.id'),
            $request->input('checkoutSession.id'),
            $request->input('payment.checkoutSession'),
            $request->input('payment.checkout.id'),
            $request->input('payment.checkoutSession.id'),
        ])->filter()->map(fn (mixed $reference): string => (string) $reference);

        return ShirtOrder::query()
            ->where('payment_gateway', 'asaas')
            ->whereIn('payment_gateway_reference', $gatewayReferences)
            ->first();
    }
}
