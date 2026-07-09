<?php

use App\Models\EventSetting;
use App\Models\Kit;
use App\Models\RaceModality;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('race landing page is available', function () {
    EventSetting::factory()->create([
        'event_date' => '12/10/2026',
        'event_location' => 'Parque Municipal',
        'kit_information' => '<p><strong>Camiseta</strong>, número de peito e medalha</p><ul><li>Retirada no local</li></ul>',
        'regulation' => '<blockquote><p>Documento oficial em revisão</p></blockquote>',
    ]);

    RaceModality::factory()->create([
        'name' => 'Adulto a partir de 16 anos',
        'type' => 'Adulto',
        'age_start' => 16,
        'distance' => '6 km',
        'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed',
        'description' => '<p>Percurso adulto com retorno sinalizado.</p>',
    ]);

    Kit::factory()->create([
        'name' => 'Kit Corrida',
        'price' => 50,
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSeeText('Ave Branca Run')
        ->assertSeeText('12/10/2026')
        ->assertSeeText('Parque Municipal')
        ->assertSeeText('Camiseta, número de peito e medalha')
        ->assertSeeText('Retirada no local')
        ->assertSeeText('Documento oficial em revisão')
        ->assertSee('<strong>Camiseta</strong>', false)
        ->assertSee('<ul>', false)
        ->assertSee('<blockquote>', false)
        ->assertSee('role="tablist"', false)
        ->assertSee('role="tabpanel"', false)
        ->assertSee('href="#percurso-', false)
        ->assertSee('https://www.google.com/maps?q=Parque%20Municipal&amp;output=embed', false)
        ->assertSeeText('Percurso adulto com retorno sinalizado.')
        ->assertSeeText('6 km')
        ->assertSeeText('Pagamento');
});

test('registration page is available', function () {
    EventSetting::factory()->create([
        'regulation' => '<p>Regulamento oficial da prova</p>',
    ]);

    RaceModality::factory()->create([
        'name' => 'Adulto a partir de 16 anos',
        'type' => 'Adulto',
        'age_start' => 16,
        'distance' => '6 km',
    ]);

    Kit::factory()->create([
        'name' => 'Kit Corrida',
        'price' => 50,
    ]);

    $this->get('/inscricao')
        ->assertSuccessful()
        ->assertSeeText('Dados para inscrição')
        ->assertSeeText('Adulto a partir de 16 anos - 6 km')
        ->assertSeeText('Kit Corrida')
        ->assertSee('data-modal-open="registration-regulation-modal"', false)
        ->assertSee('data-modal-open="registration-privacy-policy-modal"', false)
        ->assertSeeText('Regulamento oficial da prova')
        ->assertSeeText('Política de Privacidade')
        ->assertSeeText('Direitos LGPD')
        ->assertSeeText('Retenção, exclusão e anonimização')
        ->assertSeeText('Comunicações promocionais')
        ->assertSeeText('Saúde e suporte emergencial')
        ->assertSeeText('desconto legal de 50%')
        ->assertSeeText('Enviar inscrição');
});
