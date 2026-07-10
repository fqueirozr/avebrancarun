<x-mail::message>
# Sua inscrição foi atualizada

Olá {{ $registration->athlete_name }},

Temos uma novidade sobre sua inscrição na **Ave Branca Run**.

<x-mail::panel>
**Status do pagamento:** {{ $registration->paymentStatusLabel() }}<br>
**Protocolo:** {{ $registration->protocol_number }}<br>
**Última atualização:** {{ now()->format('d/m/Y H:i') }}
</x-mail::panel>

<x-mail::table>
| Dado | Informação |
| :--- | :--- |
| Atleta | {{ $registration->athlete_name }} |
| Prova | {{ $registration->modality }} |
</x-mail::table>

@if ($registration->payment_status === 'cancelled')
Esta inscrição foi cancelada. Em caso de dúvidas, entre em contato com a organização para revisar os detalhes.
@else
Guarde esta mensagem como comprovante da atualização mais recente da sua inscrição.
@endif

<x-mail::button :url="URL::signedRoute('athlete.show', ['registration' => $registration])">
Ver minha inscrição
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
