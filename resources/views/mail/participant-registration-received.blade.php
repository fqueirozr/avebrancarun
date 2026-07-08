<x-mail::message>
# Inscricao recebida

Ola {{ $registration->athlete_name }},

A sua inscricao para a **Corrida Ave Branca** ja esta em analise pela organizacao. Confira os dados registrados abaixo.

<x-mail::panel>
**Status da inscricao:** Recebida<br>
**Status do pagamento:** Pendente
</x-mail::panel>

<x-mail::table>
| Dado | Informacao |
| :--- | :--- |
| Atleta | {{ $registration->athlete_name }} |
| Data de nascimento | {{ $registration->birth_date->format('d/m/Y') }} |
| CPF do participante | {{ $registration->participant_cpf }} |
| Responsavel | {{ $registration->guardian_name ?: 'Nao informado' }} |
| CPF do responsavel | {{ $registration->guardian_cpf ?: 'Nao informado' }} |
| Telefone | {{ $registration->phone }} |
| Email | {{ $registration->email }} |
| Pagador | {{ $registration->billing_name ?: 'Nao informado' }} |
| CPF/CNPJ do pagador | {{ $registration->billing_document ?: 'Nao informado' }} |
| Modalidade | {{ $registration->modality }} |
| Observacoes | {{ $registration->notes ?: 'Nao informado' }} |
</x-mail::table>

A confirmacao final sera enviada assim que o pagamento for definido. Guarde este e-mail para consultar os dados da sua inscricao.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
