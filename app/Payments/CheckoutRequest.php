<?php

namespace App\Payments;

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use App\Models\ShirtOrder;

class CheckoutRequest
{
    public function __construct(
        public readonly ?ParticipantRegistration $registration = null,
        public readonly ?RaceModality $raceModality = null,
        public readonly ?Kit $kit = null,
        public readonly string $successUrl = '',
        public readonly string $cancelUrl = '',
        public readonly string $expiredUrl = '',
        public readonly ?ShirtOrder $shirtOrder = null,
    ) {}
}
