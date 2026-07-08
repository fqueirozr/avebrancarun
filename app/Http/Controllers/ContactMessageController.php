<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $contactMessage = ContactMessage::create($request->validated());
        $contactEmail = EventSetting::current()->contact_email;

        if (filled($contactEmail)) {
            Mail::to($contactEmail)->send(new ContactMessageReceived($contactMessage));
        }

        return redirect(route('home').'#contato')
            ->with('contact_status', 'Mensagem enviada com sucesso. A organizaÃ§Ã£o entrarÃ¡ em contato em breve.');
    }
}
