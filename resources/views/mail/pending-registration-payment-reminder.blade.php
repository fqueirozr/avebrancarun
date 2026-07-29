<x-mail::message>
# Pagamento aguardando confirmação

Olá {{ $registration->athlete_name }},

Sua inscrição na **Ave Branca Run** continua aguardando pagamento.

<x-mail::panel>
**Protocolo:** {{ $registration->protocol_number }}<br>
**Status do pagamento:** {{ $registration->paymentStatusLabel() }}
</x-mail::panel>

Acesse sua página do atleta para consultar a inscrição e concluir o pagamento.

<x-mail::button :url="$paymentUrl">
Ir para a página do atleta
</x-mail::button>

Se você já realizou o pagamento, aguarde a confirmação e desconsidere este lembrete.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
