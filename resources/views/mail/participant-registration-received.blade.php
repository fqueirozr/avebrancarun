<x-mail::message>
# Inscrição recebida ✓

Olá {{ $registration->athlete_name }},

@if ($registration->payment_status === 'under_review')
Recebemos sua inscrição e o comprovante do Pix para a **Ave Branca Run**. O pagamento agora será conferido pela organização.
@elseif ($paymentUrl)
Recebemos sua inscrição para a **Ave Branca Run**. Conclua o pagamento pelo link seguro abaixo para manter sua vaga.
@else
Recebemos sua inscrição para a **Ave Branca Run**. Acompanhe abaixo a situação registrada.
@endif

<x-mail::panel>
**Status da inscrição:** {{ $registration->payment_status === 'paid' ? 'Confirmada' : 'Recebida' }}<br>
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
| {{ $shirtOrder->shirt->name }} | {{ $shirtOrder->sizeSummary() }} | {{ $shirtOrder->quantity }} | R$ {{ number_format((float) $shirtOrder->unit_price, 2, ',', '.') }} | R$ {{ number_format((float) $shirtOrder->total_price, 2, ',', '.') }} |
@endforeach
</x-mail::table>
@endif

<x-mail::panel>
**Valor total do recibo:** R$ {{ number_format($registration->priceFor($registration->kit), 2, ',', '.') }}<br>
**Situação do pagamento:** {{ $registration->paymentStatusLabel() }}
</x-mail::panel>

Este e-mail serve como recibo da inscrição e dos itens acima.

@if ($registration->payment_status === 'under_review')
O comprovante foi recebido em área privada. O envio não confirma automaticamente o pagamento; você receberá uma atualização após a conferência da organização.
@elseif ($registration->payment_status === 'pending')
A inscrição permanece pendente até a confirmação do pagamento.
@endif

Por segurança, CPF/CNPJ, endereço, comprovante e informações de saúde não são exibidos neste e-mail.

@if ($paymentUrl)
<x-mail::button :url="$paymentUrl" color="success">
Realizar pagamento
</x-mail::button>
@endif

<x-mail::button :url="URL::signedRoute('athlete.show', ['registration' => $registration])">
Ver minha inscrição
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
