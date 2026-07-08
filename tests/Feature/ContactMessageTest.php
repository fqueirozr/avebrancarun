<?php

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\EventSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('landing page shows the contact form instead of the old information section', function () {
    EventSetting::factory()->create([
        'contact_email' => 'organizacao@example.com',
        'contact_phone' => '(11) 3333-4444',
        'contact_whatsapp' => '(11) 99999-0000',
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Fale com a organização')
        ->assertSee('organizacao@example.com')
        ->assertSee('Enviar mensagem')
        ->assertDontSee('Fluxo pronto para evoluir para pagamento online');
});

test('a visitor can send a contact message to the event email', function () {
    Mail::fake();

    EventSetting::factory()->create([
        'contact_email' => 'organizacao@example.com',
    ]);

    $this->post(route('contact.store'), [
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '(11) 99999-9999',
        'subject' => 'Retirada do kit',
        'message' => 'Gostaria de saber o horário de retirada do kit.',
    ])
        ->assertRedirect(route('home').'#contato')
        ->assertSessionHas('contact_status');

    $this->assertDatabaseHas(ContactMessage::class, [
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '11999999999',
        'subject' => 'Retirada do kit',
        'message' => 'Gostaria de saber o horário de retirada do kit.',
    ]);

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail): bool {
        return $mail->hasTo('organizacao@example.com')
            && $mail->contactMessage->email === 'maria@example.com'
            && $mail->contactMessage->phone === '11999999999';
    });
});

test('contact message validates required fields', function () {
    $this->post(route('contact.store'), [])
        ->assertSessionHasErrors([
            'name',
            'email',
            'message',
        ]);
});
