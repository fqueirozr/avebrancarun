<x-mail::message>
# Atualização da inscrição

Olá {{ $registration->athlete_name }},

A sua inscrição para a **Ave Branca Run** recebeu uma nova atualização da organização.

<x-mail::panel>
**Status do pagamento:** {{ $registration->paymentStatusLabel() }}<br>
**Protocolo:** {{ $registration->protocol_number }}<br>
**Última atualização:** {{ now()->format('d/m/Y H:i') }}
</x-mail::panel>

<x-mail::table>
| Dado | Informação |
| :--- | :--- |
| Atleta | {{ $registration->athlete_name }} |
| Sexo | {{ $registration->sexLabel() }} |
| Prova | {{ $registration->modality }} |
| Telefone | {{ $registration->phone }} |
| Email | {{ $registration->email }} |
</x-mail::table>

@if ($registration->payment_status === 'cancelled')
Esta inscrição foi cancelada. Em caso de dúvidas, entre em contato com a organização para revisar os detalhes.
@else
Guarde esta mensagem como comprovante da atualização mais recente da sua inscrição.
@endif

<x-mail::button :url="URL::signedRoute('athlete.show', ['registration' => $registration])">
Acessar página do atleta
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
