<x-mail::message>
# {{ $updateTitle }}

Olá {{ $shirtOrder->customer_name }},

Temos uma atualização sobre seu item avulso da **Ave Branca Run**.

<x-mail::panel>
**Número do pedido:** {{ $shirtOrder->id }}<br>
**Status do pagamento:** {{ $shirtOrder->paymentStatusOptions()[$shirtOrder->payment_status] ?? $shirtOrder->payment_status }}<br>
**Última atualização:** {{ now()->format('d/m/Y H:i') }}
</x-mail::panel>

<x-mail::table>
| Item | Tamanho | Quantidade | Total |
| :--- | :---: | ---: | ---: |
| {{ $shirtOrder->shirt->name }} | {{ $shirtOrder->size }} | {{ $shirtOrder->quantity }} | R$ {{ number_format((float) $shirtOrder->total_price, 2, ',', '.') }} |
</x-mail::table>

@if ($shirtOrder->payment_status === 'cancelled')
O pedido foi cancelado. Em caso de dúvidas, entre em contato com a organização.
@else
Guarde esta mensagem como comprovante da atualização mais recente do pagamento.
@endif

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
