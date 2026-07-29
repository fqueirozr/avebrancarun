<?php

use App\Models\EventSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows important registration information and additional questions', function () {
    EventSetting::factory()->create([
        'general_information' => '<p>Inscrições abertas até agosto.</p>',
        'faq_items' => [
            ['question' => 'Posso alterar meus dados?', 'answer' => '<p>Procure a organização.</p>'],
        ],
    ]);

    $this->get(route('faq'))
        ->assertSuccessful()
        ->assertSee('Tudo para você chegar')
        ->assertSee('data-faq-list', false)
        ->assertSee('Informações importantes das inscrições')
        ->assertSee('Inscrições abertas até agosto.')
        ->assertSee('Posso alterar meus dados?')
        ->assertSee('Procure a organização.');
});
