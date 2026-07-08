<?php

namespace App\Payments;

class CheckoutResponse
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly string $gateway,
        public readonly string $reference,
        public readonly string $checkoutUrl,
        public readonly array $payload = [],
    ) {}
}
