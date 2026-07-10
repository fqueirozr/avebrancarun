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
        'race_date' => '2026-10-12',
        'race_time' => '07:30:00',
        'google_maps_embed_url' => 'https://www.google.com/maps?q=Parque%20Municipal&output=embed',
        'course_information' => '<p>Percurso adulto com retorno sinalizado.</p>',
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
        ->assertSeeText('12/10/2026 às 07:30')
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
        'photo_path' => 'kits/kit-corrida.jpg',
        'description' => 'Camiseta e número de peito.',
        'price' => 50,
        'is_half_registration' => true,
    ]);

    $this->get('/inscricao')
        ->assertSuccessful()
        ->assertSeeText('Dados para inscrição')
        ->assertSeeText('Adulto a partir de 16 anos - 6 km')
        ->assertSeeText('Kit Corrida')
        ->assertSeeText('Camiseta e número de peito.')
        ->assertSeeText('R$ 50,00')
        ->assertSeeText('Kit específico para PCD, pessoas com 60 anos ou mais e Meia Social. O preço exibido já inclui o desconto.')
        ->assertDontSee('Foto do Kit Corrida')
        ->assertSee('data-modal-open="registration-regulation-modal"', false)
        ->assertSee('data-modal-open="registration-privacy-policy-modal"', false)
        ->assertSeeText('Regulamento oficial da prova')
        ->assertSeeText('Política de Privacidade')
        ->assertSeeText('Direitos LGPD')
        ->assertSeeText('Retenção, exclusão e anonimização')
        ->assertSeeText('Comunicações promocionais')
        ->assertSeeText('Saúde e suporte emergencial')
        ->assertSeeText('Vai se inscrever como PCD, 60+ ou Meia Social? Confira as dicas:')
        ->assertSeeText('Desconto já aplicado: Os valores dessas categorias já estão com o desconto incluso no app (e não é preciso anexar o laudo PCD na inscrição).')
        ->assertSeeText('No dia da retirada do kit:')
        ->assertSeeText('PCD e 60+: Basta apresentar seu documento de comprovação.')
        ->assertSeeText('Meia Social: Pedimos a gentileza de levar o alimento não perecível para doação.')
        ->assertSeeText('Enviar inscrição')
        ->assertSeeText('O número de protocolo será gerado automaticamente ao salvar os dados da inscrição no banco de dados.');
});
