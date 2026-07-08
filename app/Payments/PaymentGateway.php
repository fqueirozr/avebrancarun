<?php

namespace App\Payments;

interface PaymentGateway
{
    public function createCheckout(CheckoutRequest $request): CheckoutResponse;
}
