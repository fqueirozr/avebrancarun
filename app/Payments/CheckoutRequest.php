<?php

namespace App\Payments;

use App\Models\ParticipantRegistration;
use App\Models\RaceModality;

class CheckoutRequest
{
    public function __construct(
        public readonly ParticipantRegistration $registration,
        public readonly RaceModality $raceModality,
        public readonly string $successUrl,
        public readonly string $cancelUrl,
        public readonly string $expiredUrl,
    ) {}
}
