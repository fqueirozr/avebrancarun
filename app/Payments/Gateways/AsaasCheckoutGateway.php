<?php

namespace App\Payments\Gateways;

use App\Models\PaymentGatewaySetting;
use App\Payments\CheckoutRequest;
use App\Payments\CheckoutResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class AsaasCheckoutGateway
{
    public function createCheckout(CheckoutRequest $request, PaymentGatewaySetting $settings): CheckoutResponse
    {
        $payload = $this->payload($request, $settings);
        $response = $this->sendCheckoutRequest($payload, $settings);

        $reference = (string) Arr::get($response, 'id');
        $checkoutUrl = (string) (Arr::get($response, 'link') ?: $this->checkoutUrl($settings, $reference));

        return new CheckoutResponse(
            gateway: 'asaas',
            reference: $reference,
            checkoutUrl: $checkoutUrl,
            payload: $response,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sendCheckoutRequest(array $payload, PaymentGatewaySetting $settings): array
    {
        try {
            return $this->asaasRequest($payload, $settings);
        } catch (RequestException $exception) {
            if (! $this->canRetryWithoutPix($exception, $payload)) {
                throw $exception;
            }

            $payload['billingTypes'] = ['CREDIT_CARD'];

            return $this->asaasRequest($payload, $settings);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function asaasRequest(array $payload, PaymentGatewaySetting $settings): array
    {
        return Http::baseUrl($this->baseUrl($settings))
            ->withHeaders([
                'access_token' => $settings->api_key,
                'User-Agent' => config('app.name', 'RunApp').'/1.0.0',
            ])
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->retry(2, 300, function (Throwable $exception, PendingRequest $request): bool {
                return $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->serverError());
            })
            ->post('/v3/checkouts', $payload)
            ->throw()
            ->json();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(CheckoutRequest $request, PaymentGatewaySetting $settings): array
    {
        if ($request->shirtOrder !== null) {
            return $this->shirtOrderPayload($request, $settings);
        }

        $registration = $request->registration;
        $raceModality = $request->raceModality;
        $kit = $request->kit;

        if ($registration === null || $raceModality === null || $kit === null) {
            throw new \InvalidArgumentException('Os dados do pagamento são obrigatórios.');
        }

        return [
            'billingTypes' => $settings->billing_types ?: ['PIX', 'CREDIT_CARD'],
            'chargeTypes' => $settings->charge_types ?: ['DETACHED'],
            'minutesToExpire' => $settings->checkout_minutes_to_expire ?: 60,
            'externalReference' => "participant-registration:{$registration->id}",
            'callback' => [
                'successUrl' => $request->successUrl,
                'cancelUrl' => $request->cancelUrl,
                'expiredUrl' => $request->expiredUrl,
            ],
            'items' => [
                [
                    'externalReference' => (string) $registration->id,
                    'name' => Str::limit($kit->name, 30, ''),
                    'description' => Str::limit('Inscrição Ave Branca Run - '.$raceModality->displayName().' - '.$kit->name, 150, ''),
                    'imageBase64' => $this->itemImageBase64(),
                    'quantity' => 1,
                    'value' => $registration->priceFor($kit),
                ],
            ],
            'customerData' => [
                'name' => $registration->billing_name,
                'cpfCnpj' => $registration->billing_document,
                'email' => $registration->email,
                'phone' => preg_replace('/\D+/', '', $registration->phone) ?: $registration->phone,
                'address' => $registration->billing_address,
                'addressNumber' => $registration->billing_address_number,
                'province' => $registration->billing_province,
                'postalCode' => preg_replace('/\D+/', '', $registration->billing_postal_code),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function shirtOrderPayload(CheckoutRequest $request, PaymentGatewaySetting $settings): array
    {
        $shirtOrder = $request->shirtOrder;
        $shirtOrder?->loadMissing('shirt');

        return [
            'billingTypes' => $settings->billing_types ?: ['PIX', 'CREDIT_CARD'],
            'chargeTypes' => $settings->charge_types ?: ['DETACHED'],
            'minutesToExpire' => $settings->checkout_minutes_to_expire ?: 60,
            'externalReference' => "shirt-order:{$shirtOrder?->id}",
            'callback' => [
                'successUrl' => $request->successUrl,
                'cancelUrl' => $request->cancelUrl,
                'expiredUrl' => $request->expiredUrl,
            ],
            'items' => [
                [
                    'externalReference' => (string) $shirtOrder?->id,
                    'name' => Str::limit((string) $shirtOrder?->shirt?->name, 30, ''),
                    'description' => Str::limit('Pedido avulso Ave Branca Run - '.$shirtOrder?->shirt?->name, 150, ''),
                    'imageBase64' => $this->itemImageBase64(),
                    'quantity' => $shirtOrder?->quantity,
                    'value' => (float) $shirtOrder?->unit_price,
                ],
            ],
            'customerData' => [
                'name' => $shirtOrder?->customer_name,
                'cpfCnpj' => $shirtOrder?->customer_cpf,
                'email' => $shirtOrder?->customer_email,
                'phone' => preg_replace('/\D+/', '', (string) $shirtOrder?->customer_phone),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function canRetryWithoutPix(RequestException $exception, array $payload): bool
    {
        $billingTypes = $payload['billingTypes'] ?? [];

        if (! in_array('PIX', $billingTypes, true) || ! in_array('CREDIT_CARD', $billingTypes, true)) {
            return false;
        }

        return str($exception->response->body())->lower()->contains('chave pix');
    }

    private function baseUrl(PaymentGatewaySetting $settings): string
    {
        return (string) config(
            $settings->environment === 'production'
                ? 'payments.asaas.production_base_url'
                : 'payments.asaas.sandbox_base_url'
        );
    }

    private function checkoutUrl(PaymentGatewaySetting $settings, string $reference): string
    {
        $baseUrl = (string) config(
            $settings->environment === 'production'
                ? 'payments.asaas.production_checkout_url'
                : 'payments.asaas.sandbox_checkout_url'
        );

        return "{$baseUrl}?id={$reference}";
    }

    private function itemImageBase64(): string
    {
        $path = public_path('images/ave-branca-logo.png');

        if (! is_file($path)) {
            return '';
        }

        return base64_encode((string) file_get_contents($path));
    }
}
