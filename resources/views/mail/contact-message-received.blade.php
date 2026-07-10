<x-mail::message>
# Nova mensagem pelo site

Uma nova mensagem foi enviada pelo site da **Ave Branca Run**.

<x-mail::table>
| Dado | Informação |
| :--- | :--- |
| Nome | {{ $contactMessage->name }} |
| E-mail | {{ $contactMessage->email }} |
| Telefone | {{ $contactMessage->phone ?: 'Não informado' }} |
| Assunto | {{ $contactMessage->subject ?: 'Não informado' }} |
| Enviada em | {{ $contactMessage->created_at->format('d/m/Y H:i') }} |
</x-mail::table>

<x-mail::panel>
{{ $contactMessage->message }}
</x-mail::panel>

Mensagem enviada pelo formulário de contato da {{ config('app.name') }}.
</x-mail::message>
