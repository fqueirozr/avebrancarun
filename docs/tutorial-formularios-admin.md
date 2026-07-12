# Tutorial dos formulários administrativos

Este guia explica como preparar e operar o painel da Ave Branca Run. Acesse `/admin` com um usuário administrativo.

## Ordem de configuração inicial

Configure o sistema nesta ordem para que as opções necessárias estejam disponíveis nas inscrições:

1. **Configurações > Evento**
2. **Configurações > Provas**
3. **Configurações > Kits**
4. **Configurações > Pagamento**
5. **Configurações > Usuários administrativos**

Depois disso, use **Inscrições**, **Resultados e rankings** e **Mensagens de contato** na operação diária.

## 1. Evento

Em **Configurações > Evento**, abra o registro existente ou crie um.

### Dados gerais

- **Data** e **Local:** textos exibidos ao público. Enquanto não estiverem definidos, use “A confirmar”.
- **Prazo máximo para inscrição:** data e hora após as quais novas inscrições serão bloqueadas.
- **Limite total de inscrições:** informe um inteiro maior que zero ou deixe vazio para não limitar o total.
- **Razão social** e **CNPJ do organizador:** identificam o responsável pelo evento. Digite o CNPJ como `00.000.000/0000-00`.
- **E-mail**, **Telefone** e **WhatsApp:** canais oficiais de atendimento exibidos no site.

### Informações da prova

Preencha os textos públicos de informações gerais, retirada de kit, guarda-volumes, pelotões de largada, cronometragem, inscrições especiais e regulamento. Use títulos e listas para facilitar a leitura e confirme que o regulamento corresponde aos aceites exigidos na inscrição.

## 2. Provas

Em **Configurações > Provas**, cadastre uma modalidade para cada percurso ou categoria oferecida.

- **Nome:** obrigatório e visível ao participante.
- **Tipo:** obrigatório; escolha Infantil, Juvenil, Adulto, Master ou PCD.
- **Idade inicial e final:** opcionais. Quando usadas, a idade final deve ser igual ou maior que a inicial. Essa faixa controla quais datas de nascimento podem se inscrever.
- **Distância, data e horário:** informações públicas da largada.
- **URL do Google Maps:** use uma URL de incorporação válida, normalmente iniciada por `https://www.google.com/maps/embed`.
- **Limite de atletas:** inteiro maior que zero; considera inscrições que não estejam canceladas.
- **Ordem:** inteiro a partir de zero. Números menores aparecem primeiro.
- **Ativa:** mantenha ligada somente enquanto a prova puder ser escolhida em novas inscrições.
- **Descrição e informações do percurso:** detalhe trajeto, hidratação, altimetria e avisos.
- **Fotos do percurso:** até 6 imagens, com no máximo 4 MB cada. É possível reordená-las.

Salve uma prova antes de tentar associá-la a uma inscrição.

## 3. Kits

Em **Configurações > Kits**, cadastre cada pacote disponível.

- **Nome:** obrigatório.
- **Valor:** obrigatório, numérico e igual ou maior que zero.
- **Pacote para PCD e idosos 60+:** marque somente para um pacote específico desse público. Informe em **Valor** o preço final; o checkout não calcula desconto adicional.
- **Ordem:** inteiro a partir de zero; números menores aparecem primeiro.
- **Ativo:** controla a disponibilidade para novas inscrições.
- **Foto e descrição:** opcionais e exibidas ao público.

Um kit gratuito deve ter valor `0`. Revise cuidadosamente o preço antes de ativar o checkout.

## 4. Pagamento

Em **Configurações > Pagamento**, configure o Asaas. Faça primeiro um teste completo em Sandbox.

1. Deixe **Ativar checkout** desligado durante a configuração.
2. Selecione **Asaas** e o ambiente **Sandbox** ou **Produção**.
3. Cole a API key do mesmo ambiente, gerada no Asaas em **Integrações > API Key**.
4. Defina a expiração entre 10 e 1440 minutos.
5. Selecione pelo menos um meio de pagamento: Pix ou cartão de crédito.
6. Selecione pelo menos um tipo de cobrança. Para inscrições comuns, use **Avulsa**.
7. Salve, faça uma inscrição de teste e, somente após validar o fluxo, ative o checkout.

A API key fica criptografada no banco. Não a compartilhe, não a inclua em capturas de tela e não altere a `APP_KEY` sem um procedimento de rotação. A chave de Sandbox não funciona em Produção e vice-versa.

## 5. Usuários administrativos

Em **Configurações > Usuários administrativos**, informe nome, e-mail único, senha e confirmação.

A senha deve ter ao menos 12 caracteres e conter letras maiúsculas e minúsculas, números e símbolos. Ao editar um usuário, deixe os campos de senha vazios para conservar a senha atual. Crie contas individuais; não compartilhe credenciais entre operadores.

## 6. Inscrições

As inscrições normalmente são criadas pelo formulário público e revisadas em **Inscrições**. O protocolo, os dados do gateway, o link de checkout e os registros de aceite da Política de Privacidade são somente leitura.

Ao revisar ou corrigir uma inscrição:

- confira nome, nascimento, sexo, CPF com 11 dígitos, telefone com 10 ou 11 dígitos e e-mail;
- para menor de idade, confira nome e CPF do responsável e a indicação de preenchimento pelo representante legal;
- preencha dados do pagador apenas quando necessários: CPF/CNPJ com 11 a 14 dígitos, endereço e CEP com 8 dígitos;
- selecione uma prova e um kit previamente cadastrados;
- ajuste o pagamento para Pendente, Pago ou Cancelado somente após confirmar a situação real;
- use **Cancelar inscrição** na listagem quando quiser cancelar e enviar ao atleta o e-mail de atualização;
- registre observações, contato de emergência e informações de saúde somente quando forem necessários à operação e à segurança do evento.

Os campos de saúde, CPF, contato, endereço e responsável são dados pessoais ou sensíveis. Evite copiar esses dados para planilhas, mensagens ou observações sem necessidade. Use exportação e impressão apenas para a finalidade operacional autorizada e restrinja o acesso aos arquivos gerados.

## 7. Resultados e rankings

Em **Resultados e rankings**, localize o atleta e clique em **Informar resultado**.

- **Número do peito:** obrigatório e não pode se repetir.
- **Situação:** escolha Aguardando, Concluiu, Não largou, Não concluiu ou Desclassificado.
- **Tempo de corrida:** obrigatório para quem concluiu; use `HH:MM:SS`, por exemplo `01:23:45`.
- **Categoria:** opcional, usada na classificação por categoria.

Ao salvar, o sistema recalcula automaticamente as posições geral, por sexo e por categoria. Não digite classificações manualmente sem necessidade.

## 8. Mensagens de contato

As mensagens normalmente chegam pelo formulário público. Em **Mensagens de contato**, confira nome, e-mail, telefone, assunto e mensagem, e use **Lida em** para registrar o atendimento.

Não altere o conteúdo enviado pelo usuário, exceto quando houver uma necessidade operacional clara. Não reutilize os dados de contato para divulgação sem base legal ou consentimento adequado.

## Checklist antes de abrir inscrições

- dados, prazo, limite, contato e regulamento do evento revisados;
- todas as provas com faixa etária, limite e ordem corretos;
- kits com preço final, foto e disponibilidade conferidos;
- checkout testado no ambiente correto;
- webhook do Asaas configurado e testado;
- usuários administrativos individuais criados;
- página pública e formulário de inscrição conferidos em computador e celular;
- Política de Privacidade, regulamento e declaração de aptidão atualizados e com aceite obrigatório.
