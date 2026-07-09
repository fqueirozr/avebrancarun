<?php

namespace App\Payments;

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;

class CheckoutRequest
{
    public function __construct(
        public readonly ParticipantRegistration $registration,
        public readonly RaceModality $raceModality,
        public readonly Kit $kit,
        public readonly string $successUrl,
        public readonly string $cancelUrl,
        public readonly string $expiredUrl,
    ) {}
}
