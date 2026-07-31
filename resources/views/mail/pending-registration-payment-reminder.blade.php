<x-mail::message>
# Seu pagamento ainda está pendente

Olá {{ $registration->athlete_name }},

Notamos que o pagamento da sua inscrição na **Ave Branca Run** ainda não foi identificado.

<x-mail::panel>
**Protocolo:** {{ $registration->protocol_number }}<br>
**Status do pagamento:** {{ $registration->paymentStatusLabel() }}
</x-mail::panel>

Para manter sua vaga, conclua o pagamento pelo link abaixo. Inscrições que permanecerem com o pagamento pendente por **7 dias após o cadastro** serão canceladas automaticamente.

Se você já realizou o pagamento, desconsidere este lembrete. A confirmação pode levar algum tempo para ser processada.

<x-mail::button :url="$paymentUrl" color="success">
Realizar pagamento
</x-mail::button>

Se precisar de ajuda, entre em contato com a organização. Teremos prazer em orientar você.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
