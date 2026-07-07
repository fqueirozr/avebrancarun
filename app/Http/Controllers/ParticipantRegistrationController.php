<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterParticipantRequest;
use App\Mail\ParticipantRegistrationReceived;
use App\Models\ParticipantRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ParticipantRegistrationController extends Controller
{
    public function store(RegisterParticipantRequest $request): RedirectResponse
    {
        $registration = ParticipantRegistration::create($request->validated());

        Mail::to($registration->email)->send(new ParticipantRegistrationReceived($registration));

        return to_route('registration')
            ->with('status', 'Inscricao enviada com sucesso. A confirmacao de pagamento continua pendente.');
    }
}
