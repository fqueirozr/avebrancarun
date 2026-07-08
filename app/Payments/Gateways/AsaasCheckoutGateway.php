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
        return [
            'billingTypes' => $settings->billing_types ?: ['PIX', 'CREDIT_CARD'],
            'chargeTypes' => $settings->charge_types ?: ['DETACHED'],
            'minutesToExpire' => $settings->checkout_minutes_to_expire ?: 60,
            'externalReference' => "participant-registration:{$request->registration->id}",
            'callback' => [
                'successUrl' => $request->successUrl,
                'cancelUrl' => $request->cancelUrl,
                'expiredUrl' => $request->expiredUrl,
            ],
            'items' => [
                [
                    'externalReference' => (string) $request->registration->id,
                    'name' => Str::limit($request->raceModality->displayName(), 30, ''),
                    'description' => Str::limit('Inscrição Ave Branca Run - '.$request->raceModality->displayName(), 150, ''),
                    'imageBase64' => $this->itemImageBase64(),
                    'quantity' => 1,
                    'value' => $request->registration->priceFor($request->raceModality),
                ],
            ],
            'customerData' => [
                'name' => $request->registration->billing_name,
                'cpfCnpj' => $request->registration->billing_document,
                'email' => $request->registration->email,
                'phone' => preg_replace('/\D+/', '', $request->registration->phone) ?: $request->registration->phone,
                'address' => $request->registration->billing_address,
                'addressNumber' => $request->registration->billing_address_number,
                'province' => $request->registration->billing_province,
                'postalCode' => $request->registration->billing_postal_code,
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
