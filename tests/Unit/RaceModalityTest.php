<?php

use App\Models\RaceModality;

it('oferece todos os tipos permitidos para cadastro', function () {
    expect(RaceModality::typeOptions())->toBe([
        'Infantil' => 'Infantil',
        'Juvenil' => 'Juvenil',
        'Adulto' => 'Adulto',
        'Master' => 'Master',
        'PCD' => 'PCD',
    ]);
});
