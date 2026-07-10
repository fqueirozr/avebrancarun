<x-mail::message>
# Inscrição recebida ✓

Olá {{ $registration->athlete_name }},

Recebemos sua inscrição para a **Ave Branca Run**. Agora ela está em análise pela organização.

<x-mail::panel>
**Status da inscrição:** Recebida<br>
**Protocolo:** {{ $registration->protocol_number }}<br>
**Status do pagamento:** Pendente
</x-mail::panel>

<x-mail::table>
| Dado | Informação |
| :--- | :--- |
| Protocolo | {{ $registration->protocol_number }} |
| Atleta | {{ $registration->athlete_name }} |
| Prova | {{ $registration->modality }} |
| Pagamento | Pendente |
</x-mail::table>

A confirmação final será enviada assim que o pagamento for definido. Por segurança, dados pessoais e informações de saúde não são exibidos neste e-mail.

<x-mail::button :url="URL::signedRoute('athlete.show', ['registration' => $registration])">
Ver minha inscrição
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
