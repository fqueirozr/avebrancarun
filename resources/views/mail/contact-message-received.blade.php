<x-mail::message>
# Nova mensagem de contato

Uma nova mensagem foi enviada pelo site da **Corrida Ave Branca**.

<x-mail::table>
| Dado | InformaÃ§Ã£o |
| :--- | :--- |
| Nome | {{ $contactMessage->name }} |
| E-mail | {{ $contactMessage->email }} |
| Telefone | {{ $contactMessage->phone ?: 'NÃ£o informado' }} |
| Assunto | {{ $contactMessage->subject ?: 'NÃ£o informado' }} |
| Enviada em | {{ $contactMessage->created_at->format('d/m/Y H:i') }} |
</x-mail::table>

<x-mail::panel>
{{ $contactMessage->message }}
</x-mail::panel>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
