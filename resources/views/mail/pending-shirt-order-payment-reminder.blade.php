<x-mail::message>
# Pagamento aguardando confirmação

Olá {{ $shirtOrder->customer_name }},

O pagamento do seu pedido de item avulso da **Ave Branca Run** continua pendente.

<x-mail::panel>
**Número do pedido:** {{ $shirtOrder->id }}<br>
**Item:** {{ $shirtOrder->shirt->name }}<br>
**Valor:** R$ {{ number_format((float) $shirtOrder->total_price, 2, ',', '.') }}
</x-mail::panel>

<x-mail::button :url="$paymentUrl">
Realizar pagamento
</x-mail::button>

Se você já realizou o pagamento, aguarde a confirmação e desconsidere este lembrete.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
