<?php

return [
    'default' => env('PAYMENT_GATEWAY', 'asaas'),

    'asaas' => [
        'sandbox_base_url' => env('ASAAS_SANDBOX_BASE_URL', 'https://api-sandbox.asaas.com'),
        'production_base_url' => env('ASAAS_PRODUCTION_BASE_URL', 'https://api.asaas.com'),
        'sandbox_checkout_url' => env('ASAAS_SANDBOX_CHECKOUT_URL', 'https://sandbox.asaas.com/checkoutSession/show'),
        'production_checkout_url' => env('ASAAS_PRODUCTION_CHECKOUT_URL', 'https://asaas.com/checkoutSession/show'),
    ],
];
