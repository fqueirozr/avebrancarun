<?php

use App\Models\RaceModality;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    RaceModality::factory()->create();

    $response = $this->get('/');

    $response->assertSuccessful();
});
