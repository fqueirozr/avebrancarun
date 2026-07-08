<x-mail::message>
# Inscrição recebida

Olá {{ $registration->athlete_name }},

A sua inscrição para a **Corrida Ave Branca** já está em análise pela organização. Confira os dados registrados abaixo.

<x-mail::panel>
**Status da inscrição:** Recebida<br>
**Status do pagamento:** Pendente
</x-mail::panel>

<x-mail::table>
| Dado | Informação |
| :--- | :--- |
| Atleta | {{ $registration->athlete_name }} |
| Data de nascimento | {{ $registration->birth_date->format('d/m/Y') }} |
| CPF do participante | {{ $registration->participant_cpf }} |
| Responsável | {{ $registration->guardian_name ?: 'Não informado' }} |
| CPF do responsável | {{ $registration->guardian_cpf ?: 'Não informado' }} |
| Telefone | {{ $registration->phone }} |
| Email | {{ $registration->email }} |
| Pagador | {{ $registration->billing_name ?: 'Não informado' }} |
| CPF/CNPJ do pagador | {{ $registration->billing_document ?: 'Não informado' }} |
| Modalidade | {{ $registration->modality }} |
| Observações | {{ $registration->notes ?: 'Não informado' }} |
</x-mail::table>

A confirmação final será enviada assim que o pagamento for definido. Guarde este e-mail para consultar os dados da sua inscrição.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
