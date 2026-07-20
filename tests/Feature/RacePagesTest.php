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
        'organizer_legal_name' => 'Instituto Esportivo Ave Branca',
        'organizer_cnpj' => '12.345.678/0001-95',
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
        'photo_path' => 'kits/kit-corrida.jpg',
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
        ->assertSee('src="'.asset('storage/kits/kit-corrida.jpg').'"', false)
        ->assertSee('alt="Foto do Kit Corrida"', false)
        ->assertSeeText('Pagamento')
        ->assertSeeText('Pix')
        ->assertSeeText('Pagamento rápido e seguro para concluir sua inscrição.')
        ->assertDontSeeText('Crédito/PIX')
        ->assertDontSeeText('Asaas')
        ->assertSeeText('Responsável: Instituto Esportivo Ave Branca — CNPJ 12.345.678/0001-95');
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
        'type' => Kit::TypePcd60,
    ]);

    $this->get('/inscricao')
        ->assertSuccessful()
        ->assertSeeText('Dados para inscrição')
        ->assertSeeText('Adulto a partir de 16 anos - 6 km')
        ->assertSee('data-modality-option', false)
        ->assertSee('data-age-start="16"', false)
        ->assertSee('data-race-date="2026-09-20"', false)
        ->assertSeeText('Informe a data de nascimento do atleta para visualizar as provas disponíveis para a idade dele.')
        ->assertSeeText('Kit Corrida')
        ->assertSeeText('Camiseta e número de peito.')
        ->assertSeeText('R$ 50,00')
        ->assertSeeText('Kit com desconto especial. O preço exibido já inclui o desconto.')
        ->assertSee('data-special-kit', false)
        ->assertSee('id="special-kit-rules-modal"', false)
        ->assertSeeText('Regras do kit especial')
        ->assertSeeText('Li e estou ciente das regras deste kit.')
        ->assertDontSee('Foto do Kit Corrida')
        ->assertSee('data-modal-open="registration-regulation-modal"', false)
        ->assertSee('data-modal-open="registration-privacy-policy-modal"', false)
        ->assertSeeText('Regulamento oficial da prova')
        ->assertSeeText('Política de Privacidade')
        ->assertSeeText('Direitos LGPD')
        ->assertSeeText('Retenção, exclusão e anonimização')
        ->assertDontSeeText('Comunicações promocionais')
        ->assertSeeText('Contato de emergência')
        ->assertSeeText('Declaro estar em boas condições de saúde e apto(a) para participar da corrida.')
        ->assertDontSeeText('Vai se inscrever como PCD, 60+ ou Meia Social? Confira as dicas:')
        ->assertDontSeeText('Meia Social: o desconto já está aplicado. Na retirada do kit, entregue um alimento não perecível para doação.')
        ->assertSeeText('Enviar inscrição')
        ->assertDontSeeText('Confirmação pendente')
        ->assertDontSeeText('A inscrição será registrada e o valor do kit selecionado será usado no pagamento.')
        ->assertDontSeeText('Continuar')
        ->assertDontSeeText('O número de protocolo será gerado automaticamente ao salvar os dados da inscrição no banco de dados.');
});
