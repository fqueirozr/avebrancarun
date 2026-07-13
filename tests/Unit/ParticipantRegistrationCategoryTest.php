<?php

use App\Models\ParticipantRegistration;
use Illuminate\Support\Carbon;

it('provides result categories from 6 years old through 60 plus for both sexes', function () {
    expect(ParticipantRegistration::resultCategoryOptions())
        ->toHaveCount(20)
        ->toHaveKeys([
            'Masculino 6–7',
            'Feminino 6–7',
            'Masculino 30–39',
            'Feminino 30–39',
            'Masculino 60+',
            'Feminino 60+',
        ]);
});

it('calculates the result category from sex and age on the reference date', function (string $sex, string $birthDate, ?string $expectedCategory) {
    expect(ParticipantRegistration::resultCategoryFor(
        $sex,
        Carbon::parse($birthDate),
        Carbon::parse('2026-09-20'),
    ))->toBe($expectedCategory);
})->with([
    'six year old male' => ['male', '2020-09-20', 'Masculino 6–7'],
    'thirty-nine year old female' => ['female', '1986-09-21', 'Feminino 30–39'],
    'sixty year old female' => ['female', '1966-09-20', 'Feminino 60+'],
    'younger than six' => ['male', '2021-09-20', null],
]);
