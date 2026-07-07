<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterParticipantRequest;
use App\Mail\ParticipantRegistrationReceived;
use App\Models\ParticipantRegistration;
use App\Models\RaceModality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ParticipantRegistrationController extends Controller
{
    public function store(RegisterParticipantRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $raceModality = RaceModality::query()->findOrFail($validated['race_modality_id']);

        $registration = ParticipantRegistration::create([
            ...$validated,
            'modality' => $raceModality->displayName(),
        ]);

        Mail::to($registration->email)->send(new ParticipantRegistrationReceived($registration));

        return to_route('registration')
            ->with('status', 'Inscricao enviada com sucesso. A confirmacao de pagamento continua pendente.');
    }
}
