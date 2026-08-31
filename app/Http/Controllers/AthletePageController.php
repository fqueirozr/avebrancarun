<?php

namespace App\Http\Controllers;

use App\Models\EventSetting;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\URL;

class AthletePageController extends Controller
{
    public function __invoke(ParticipantRegistration $registration): View
    {
        $registration->load(['raceModality', 'kit', 'shirtOrders.shirt']);
        $pendingPaymentUrl = null;

        if ($registration->payment_status === 'pending') {
            $pendingPaymentUrl = filled($registration->payment_checkout_url)
                ? $registration->payment_checkout_url
                : $this->manualPixUrl($registration);
        }

        return view('athlete', [
            'eventSetting' => EventSetting::current(),
            'pendingPaymentUrl' => $pendingPaymentUrl,
            'usesManualPix' => $pendingPaymentUrl !== null && blank($registration->payment_checkout_url),
            'registration' => $registration,
        ]);
    }

    private function manualPixUrl(ParticipantRegistration $registration): ?string
    {
        if (! PaymentGatewaySetting::current()->hasManualPix()) {
            return null;
        }

        return URL::temporarySignedRoute(
            'registration.pix.show',
            now()->addDays(7),
            ['registration' => $registration],
        );
    }
}
