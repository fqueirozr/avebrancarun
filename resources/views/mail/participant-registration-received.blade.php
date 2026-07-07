<x-mail::message>
# Inscricao recebida

Ola {{ $registration->athlete_name }},

Recebemos a sua inscricao para a Corrida Ave Branca. Confira abaixo os dados registados:

<x-mail::panel>
**Atleta:** {{ $registration->athlete_name }}<br>
**Data de nascimento:** {{ $registration->birth_date->format('d/m/Y') }}<br>
**Responsavel:** {{ $registration->guardian_name ?: 'Nao informado' }}<br>
**Telefone:** {{ $registration->phone }}<br>
**Email:** {{ $registration->email }}<br>
**Modalidade:** {{ $registration->modality }}<br>
**Observacoes:** {{ $registration->notes ?: 'Nao informado' }}<br>
**Estado do pagamento:** Pendente
</x-mail::panel>

A confirmacao final sera feita apos a definicao do pagamento.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
