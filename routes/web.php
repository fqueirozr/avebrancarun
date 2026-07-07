<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantRegistrationController;

Route::view('/', 'welcome')->name('home');

Route::view('/inscricao', 'registration')->name('registration');
Route::post('/inscricao', [ParticipantRegistrationController::class, 'store'])->name('registration.store');
