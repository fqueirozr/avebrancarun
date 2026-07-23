<x-mail::message>
# Pedido de item avulso recebido ✓

Olá {{ $shirtOrder->customer_name }},

Recebemos seu pedido de item avulso da **Ave Branca Run**.

<x-mail::panel>
**Número do pedido:** {{ $shirtOrder->id }}<br>
**Situação do pagamento:** Pendente
</x-mail::panel>

<x-mail::table>
| Item | Tamanho | Quantidade | Valor unitário | Total |
| :--- | :---: | ---: | ---: | ---: |
| {{ $shirtOrder->shirt->name }} | {{ $shirtOrder->size }} | {{ $shirtOrder->quantity }} | R$ {{ number_format((float) $shirtOrder->unit_price, 2, ',', '.') }} | R$ {{ number_format((float) $shirtOrder->total_price, 2, ',', '.') }} |
</x-mail::table>

Este e-mail serve como recibo do pedido. A confirmação do pagamento será informada separadamente.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
