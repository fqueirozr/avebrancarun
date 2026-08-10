<x-mail::message>
# {{ $updateTitle }}

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
@if ($registration->cancellation_source === \App\Models\ParticipantRegistration::CancellationSourceAutomatic)
Informamos que sua inscrição foi cancelada automaticamente porque não identificamos informações de pagamento dentro do prazo de 7 dias.

Sabemos que imprevistos acontecem. Se você ainda deseja participar ou acredita que o pagamento já foi realizado, entre em contato com a organização para que possamos orientar você.
@else
Informamos que sua inscrição foi cancelada.

Caso tenha dúvidas ou precise de maiores esclarecimentos, a equipe da **Ave Branca Run** está à disposição para ajudar.
@endif
@else
@if ($registration->payment_status === 'paid')
O pagamento foi confirmado e sua inscrição está confirmada para as operações do evento.
@elseif ($registration->payment_status === 'under_review')
Seu comprovante foi recebido e permanece em conferência pela organização. Esse status ainda não confirma o pagamento.
@else
Guarde esta mensagem como comprovante da atualização mais recente da sua inscrição.
@endif
@endif

<x-mail::button :url="URL::signedRoute('athlete.show', ['registration' => $registration])">
Ver minha inscrição
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
