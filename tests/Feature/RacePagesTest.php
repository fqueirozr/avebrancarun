<?php

test('race landing page is available', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSeeText('Corrida Ave Branca')
        ->assertSeeText('6 km')
        ->assertSeeText('Pagamento');
});

test('registration page is available', function () {
    $this->get('/inscricao')
        ->assertSuccessful()
        ->assertSeeText('Dados para inscricao')
        ->assertSeeText('Adulto a partir de 16 anos - 6 km')
        ->assertSeeText('Enviar inscricao');
});
