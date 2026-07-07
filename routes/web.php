<?php

use App\Http\Controllers\ParticipantRegistrationController;
use App\Models\EventSetting;
use App\Models\RaceModality;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'eventSetting' => EventSetting::current(),
        'modalities' => RaceModality::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
    ]);
})->name('home');

Route::get('/inscricao', function () {
    return view('registration', [
        'modalities' => RaceModality::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
    ]);
})->name('registration');
Route::post('/inscricao', [ParticipantRegistrationController::class, 'store'])->name('registration.store');
