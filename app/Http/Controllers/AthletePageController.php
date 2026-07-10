<?php

namespace App\Http\Controllers;

use App\Models\EventSetting;
use App\Models\ParticipantRegistration;
use Illuminate\Contracts\View\View;

class AthletePageController extends Controller
{
    public function __invoke(ParticipantRegistration $registration): View
    {
        $registration->load(['raceModality', 'kit']);

        return view('athlete', [
            'eventSetting' => EventSetting::current(),
            'registration' => $registration,
        ]);
    }
}
