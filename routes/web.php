<?php

use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\AthletePageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\ParticipantRegistrationController;
use App\Http\Controllers\ShirtOrderController;
use App\Models\EventSetting;
use App\Models\Kit;
use App\Models\RaceModality;
use App\Models\Shirt;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'eventSetting' => EventSetting::current(),
        'modalities' => RaceModality::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
        'kits' => Kit::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
        'shirts' => Shirt::query()->where('is_active', true)->orderBy('name')->get(),
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
        'kits' => Kit::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(),
        'shirts' => Shirt::query()->where('is_active', true)->orderBy('name')->get(),
    ]);
})->name('registration');
Route::post('/inscricao', [ParticipantRegistrationController::class, 'store'])->name('registration.store');
Route::post('/inscricao/desbravador', [ParticipantRegistrationController::class, 'checkPathfinderEligibility'])
    ->middleware('throttle:30,1')
    ->name('registration.pathfinder.check');
Route::get('/loja', [ShirtOrderController::class, 'index'])->name('store.index');
Route::post('/loja', [ShirtOrderController::class, 'store'])->name('store.store');
Route::redirect('/camiseta', '/loja');
Route::redirect('/camisetas', '/loja');
Route::get('/inscricao/{registration}/pix', [ParticipantRegistrationController::class, 'showPix'])
    ->middleware('signed')
    ->name('registration.pix.show');
Route::post('/inscricao/{registration}/pix', [ParticipantRegistrationController::class, 'storePixReceipt'])
    ->middleware('signed')
    ->name('registration.pix.store');
Route::get('/atleta/{registration}', AthletePageController::class)
    ->middleware('signed')
    ->name('athlete.show');
Route::post('/contato', [ContactMessageController::class, 'store'])->name('contact.store');
Route::get('/inscricao/{registration}/pagamento/sucesso', [ParticipantRegistrationController::class, 'paymentSuccess'])
    ->middleware('signed')
    ->name('registration.payment.success');
Route::get('/inscricao/pagamento/sucesso', [ParticipantRegistrationController::class, 'paymentSuccessNotice'])->name('registration.payment.success.notice');
Route::get('/inscricao/pagamento/cancelado', [ParticipantRegistrationController::class, 'paymentCancel'])->name('registration.payment.cancel');
Route::get('/inscricao/pagamento/expirado', [ParticipantRegistrationController::class, 'paymentExpired'])->name('registration.payment.expired');
Route::post('/webhooks/asaas', AsaasWebhookController::class)->name('webhooks.asaas');
