<?php

use App\Models\Kit;
use App\Models\ParticipantRegistration;

test('the registered package price is final', function (string $type) {
    $registration = new ParticipantRegistration;
    $kit = new Kit([
        'price' => 80,
        'type' => $type,
    ]);

    expect($registration->priceFor($kit))->toBe(80.0);
})->with([
    'package for PCD and elderly people' => [Kit::TypePcd60],
    'regular package' => [Kit::TypeStandard],
]);
