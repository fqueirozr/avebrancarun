<?php

use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\ContactMessageController;
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
        'eventSetting' => EventSetting::current(),
        'modalities' => RaceModality::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
    ]);
})->name('registration');
Route::post('/inscricao', [ParticipantRegistrationController::class, 'store'])->name('registration.store');
Route::post('/contato', [ContactMessageController::class, 'store'])->name('contact.store');
Route::get('/inscricao/{registration}/pagamento/sucesso', [ParticipantRegistrationController::class, 'paymentSuccess'])
    ->middleware('signed')
    ->name('registration.payment.success');
Route::get('/inscricao/pagamento/sucesso', [ParticipantRegistrationController::class, 'paymentSuccessNotice'])->name('registration.payment.success.notice');
Route::get('/inscricao/pagamento/cancelado', [ParticipantRegistrationController::class, 'paymentCancel'])->name('registration.payment.cancel');
Route::get('/inscricao/pagamento/expirado', [ParticipantRegistrationController::class, 'paymentExpired'])->name('registration.payment.expired');
Route::post('/webhooks/asaas', AsaasWebhookController::class)->name('webhooks.asaas');
