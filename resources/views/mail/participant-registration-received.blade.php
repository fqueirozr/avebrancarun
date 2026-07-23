<x-mail::message>
# Inscrição recebida ✓

Olá {{ $registration->athlete_name }},

Recebemos sua inscrição para a **Ave Branca Run**. Agora ela está em análise pela organização.

<x-mail::panel>
**Status da inscrição:** Recebida<br>
**Protocolo:** {{ $registration->protocol_number }}<br>
**Status do pagamento:** {{ $registration->paymentStatusLabel() }}
</x-mail::panel>

<x-mail::table>
| Dado | Informação |
| :--- | :--- |
| Protocolo | {{ $registration->protocol_number }} |
| Atleta | {{ $registration->athlete_name }} |
| Prova | {{ $registration->modality }} |
| Inscrição / pacote | R$ {{ number_format((float) $registration->kit->price, 2, ',', '.') }} |
| Pagamento | {{ $registration->paymentStatusLabel() }} |
</x-mail::table>

@if ($registration->shirtOrders->isNotEmpty())
## Item avulso adicionado à inscrição

<x-mail::table>
| Item | Tamanho | Quantidade | Valor unitário | Total |
| :--- | :---: | ---: | ---: | ---: |
@foreach ($registration->shirtOrders as $shirtOrder)
| {{ $shirtOrder->shirt->name }} | {{ $shirtOrder->size }} | {{ $shirtOrder->quantity }} | R$ {{ number_format((float) $shirtOrder->unit_price, 2, ',', '.') }} | R$ {{ number_format((float) $shirtOrder->total_price, 2, ',', '.') }} |
@endforeach
</x-mail::table>
@endif

<x-mail::panel>
**Valor total do recibo:** R$ {{ number_format($registration->priceFor($registration->kit), 2, ',', '.') }}<br>
**Situação do pagamento:** {{ $registration->paymentStatusLabel() }}
</x-mail::panel>

Este e-mail serve como recibo da inscrição e dos itens acima. A confirmação final será enviada assim que o pagamento for definido. Por segurança, dados pessoais e informações de saúde não são exibidos neste e-mail.

<x-mail::button :url="URL::signedRoute('athlete.show', ['registration' => $registration])">
Ver minha inscrição
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
