<x-mail::message>
# Atualizacao da inscricao

Ola {{ $registration->athlete_name }},

A sua inscricao para a **Corrida Ave Branca** recebeu uma nova atualizacao da organizacao.

<x-mail::panel>
**Status do pagamento:** {{ $registration->paymentStatusLabel() }}<br>
**Ultima atualizacao:** {{ now()->format('d/m/Y H:i') }}
</x-mail::panel>

<x-mail::table>
| Dado | Informacao |
| :--- | :--- |
| Atleta | {{ $registration->athlete_name }} |
| Modalidade | {{ $registration->modality }} |
| Telefone | {{ $registration->phone }} |
| Email | {{ $registration->email }} |
</x-mail::table>

@if ($registration->payment_status === 'cancelled')
Esta inscricao foi cancelada. Em caso de duvidas, entre em contato com a organizacao para revisar os detalhes.
@else
Guarde esta mensagem como comprovante da atualizacao mais recente da sua inscricao.
@endif

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
