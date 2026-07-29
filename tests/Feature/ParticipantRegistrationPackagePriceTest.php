<?php

use App\Models\Kit;
use App\Models\ParticipantRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

test('a large included shirt adds the configured package surcharge', function () {
    $registration = new ParticipantRegistration(['shirt_size' => '2XG']);
    $kit = new Kit([
        'price' => 80,
        'has_shirt' => true,
        'size_2xl_surcharge' => 12,
        'size_3xl_surcharge' => 18,
    ]);

    expect($registration->priceFor($kit))->toBe(92.0);
});
