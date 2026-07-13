# Tutorial dos formulários administrativos

Este guia explica como preparar e operar o painel da Ave Branca Run. Acesse `/admin` com um usuário administrativo.

## Ordem de configuração inicial

Configure o sistema nesta ordem para que as opções necessárias estejam disponíveis nas inscrições:

1. **Configurações > Evento**
2. **Configurações > Provas**
3. **Configurações > Kits**
4. **Secretaria > Desbravadores**, quando houver programa de indicação
5. **Configurações > Pagamento**
6. **Configurações > Usuários administrativos**

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
- **Tipo:** escolha Normal, PCD / 60+, Kit Social ou Desbravador. Todo tipo diferente de Normal exige aceite das regras na inscrição.
- **Regras exibidas no modal:** explique critérios, documentos, contrapartidas ou condições do tipo especial. O participante precisa declarar ciência antes de concluir.
- **Quantidade máxima:** limita inscrições não canceladas vinculadas ao kit. Deixe vazio para não limitar.
- **Upgrades do desbravador:** para kits do tipo Desbravador, configure até três quantidades de indicações e descreva o item acrescentado em cada nível. Os benefícios são cumulativos.
- **Ordem:** inteiro a partir de zero; números menores aparecem primeiro.
- **Ativo:** controla a disponibilidade para novas inscrições.
- **Foto e descrição:** opcionais e exibidas ao público.

Um kit gratuito deve ter valor `0`. O valor cadastrado é sempre o preço final: o checkout não aplica desconto adicional para PCD, idosos ou kit social. Revise preço, regras e estoque antes de ativar o checkout.

## 4. Desbravadores

Em **Secretaria > Desbravadores**, cadastre cada participante do programa de indicação.

- **Nome:** identifica o desbravador no painel.
- **Código:** é gerado automaticamente com quatro dígitos e pode ser copiado na listagem.
- **Ativo:** permite ou bloqueia o uso do código em novas inscrições.
- **Indicações:** a listagem contabiliza inscrições normais associadas ao código.

O desbravador usa o próprio código ao selecionar um kit do tipo **Desbravador**. Cada código aceita apenas uma inscrição desse tipo. Participantes com kit Normal podem informar o código para contar uma indicação; kits PCD / 60+ e Social não aceitam indicação. Conforme o total aumenta, o sistema atualiza automaticamente o nível e os acréscimos do kit do desbravador.

## 5. Pagamento

Em **Configurações > Pagamento**, configure o Asaas. Faça primeiro um teste completo em Sandbox.

1. Deixe **Ativar checkout** desligado durante a configuração.
2. Selecione **Asaas** e o ambiente **Sandbox** ou **Produção**.
3. Cole a API key do mesmo ambiente, gerada no Asaas em **Integrações > API Key**.
4. Defina a expiração entre 10 e 1440 minutos.
5. Selecione pelo menos um meio de pagamento: Pix ou cartão de crédito.
6. Selecione pelo menos um tipo de cobrança. Para inscrições comuns, use **Avulsa**.
7. Salve, faça uma inscrição de teste e, somente após validar o fluxo, ative o checkout.

A API key fica criptografada no banco. Não a compartilhe, não a inclua em capturas de tela e não altere a `APP_KEY` sem um procedimento de rotação. A chave de Sandbox não funciona em Produção e vice-versa.

Além da API key, configure `ASAAS_WEBHOOK_TOKEN` no ambiente da aplicação e cadastre o mesmo segredo no webhook do Asaas. Requisições sem o cabeçalho `asaas-access-token` correto são rejeitadas.

## 6. Usuários administrativos

Em **Configurações > Usuários administrativos**, informe nome, e-mail único, senha e confirmação.

A senha deve ter ao menos 12 caracteres e conter letras maiúsculas e minúsculas, números e símbolos. Ao editar um usuário, deixe os campos de senha vazios para conservar a senha atual. Crie contas individuais; não compartilhe credenciais entre operadores.

## 7. Inscrições

As inscrições normalmente são criadas pelo formulário público em `/inscricao` e revisadas no painel em **Inscrições**. Antes de divulgar o formulário, confirme que o evento está dentro do prazo, que o limite geral ainda não foi atingido e que existe pelo menos uma prova e um kit ativos.

### 7.1. Antes de começar

Oriente o participante a separar:

- dados pessoais e de contato do atleta;
- CPF do atleta, pois cada CPF pode possuir somente uma inscrição;
- nome e CPF do responsável legal, quando o atleta for menor de 18 anos ou não puder responder por si;
- dados do pagador: nome completo, CPF ou CNPJ, endereço, número, bairro e CEP;
- telefone de um contato de emergência e informações de saúde relevantes, quando houver;
- código de desbravador com quatro dígitos, quando a modalidade de kit permitir.

Máscaras como pontos, traços e parênteses ajudam no preenchimento, mas o sistema valida os números. Um CPF ou CNPJ com quantidade correta de dígitos, porém matematicamente inválido, não é aceito.

### 7.2. Etapa Atleta

Preencha o nome completo, CPF, data de nascimento, sexo, telefone e e-mail do atleta.

- A data de nascimento deve ser anterior ao dia atual e será usada para liberar somente provas compatíveis com a idade na data de referência do evento.
- O CPF deve ser válido e não pode estar associado a outra inscrição, inclusive uma inscrição ainda pendente de pagamento.
- O telefone deve ter DDD e 10 ou 11 dígitos.
- O e-mail deve estar correto, pois recebe o protocolo, o status e o link assinado para a página do atleta.
- Marque **Estou preenchendo como representante legal** em casos de menoridade, tutela, curatela ou impossibilidade do titular responder por si.

Quando a data de nascimento identificar um menor de 18 anos, o sistema considera automaticamente que o formulário foi preenchido por representante legal.

### 7.3. Etapa Responsável Legal

Esta etapa aparece quando há representação legal. Informe nome completo e CPF válido do responsável. Para menores de idade, os dois campos são obrigatórios mesmo que a caixa de representação não tenha sido marcada manualmente.

Esses dados servem para registrar a autorização e a responsabilidade pela inscrição do menor. Não use os dados do responsável como substitutos dos dados do atleta: cada pessoa deve permanecer em seu campo próprio.

### 7.4. Etapa Pagador

Informe os dados da pessoa física ou jurídica responsável pelo pagamento:

- nome completo ou razão social;
- CPF com 11 dígitos ou CNPJ com 14 dígitos;
- rua, avenida ou travessa;
- número do endereço;
- bairro;
- CEP com 8 dígitos.

Esses campos são obrigatórios mesmo quando o atleta e o pagador são a mesma pessoa. Eles são enviados ao Asaas somente no limite necessário para criar e reconciliar a cobrança. Dados completos de cartão, chave Pix ou boleto não são armazenados pela aplicação.

### 7.5. Etapa Prova

Após informar a data de nascimento, escolha uma das provas exibidas como disponíveis.

O formulário pode impedir a seleção quando:

- a prova está inativa;
- a idade do atleta não pertence à faixa configurada;
- o limite de participantes da prova foi atingido;
- o prazo geral de inscrições terminou;
- o limite total do evento foi atingido.

A conferência é repetida no envio do formulário. Assim, se a última vaga for ocupada por outra pessoa enquanto o participante preenche os dados, o sistema não ultrapassa o limite: ele retorna uma mensagem e solicita uma nova escolha.

### 7.6. Etapa Kit

Selecione o kit, confira o preço final e escolha o tamanho da camisa entre PP, P, M, G, GG e XGG. A disponibilidade é verificada novamente ao enviar, portanto um kit esgotado não será confirmado mesmo que ainda estivesse visível quando a página foi aberta.

As regras variam conforme o tipo:

- **Normal:** pode aceitar opcionalmente um código de indicação de desbravador ativo;
- **PCD / 60+:** exige leitura e confirmação das regras próprias; eventual comprovação ocorre conforme o texto configurado para o kit;
- **Kit Social:** exige leitura e confirmação das contrapartidas configuradas;
- **Desbravador:** exige o código ativo do próprio desbravador, e cada código pode ser vinculado a apenas uma inscrição desse tipo.

Kits PCD / 60+ e Social não aceitam código de indicação. Quando um código válido é usado em um kit Normal, a indicação é contabilizada e pode atualizar os acréscimos acumulados no kit do desbravador.

Ao escolher um kit especial, o sistema abre um modal. Leia o conteúdo, marque **Li e estou ciente das regras deste kit** e confirme. Sem esse aceite, a inscrição não pode ser concluída. O valor apresentado no kit já é o preço final; não existe desconto adicional no checkout.

### 7.7. Etapa Observações

Os campos desta etapa são opcionais e têm finalidades diferentes:

- **Observações gerais:** equipe, preferência operacional ou informação não sensível necessária à organização;
- **Contato de emergência:** nome e telefone de quem deve ser acionado em uma emergência;
- **Saúde e emergência:** alergias, medicamentos, restrições médicas ou condições relevantes ao atendimento durante o evento.

Não repita dados de saúde em observações gerais. Informações de saúde são dados pessoais sensíveis e devem ser inseridas somente quando realmente necessárias à segurança do atleta.

### 7.8. Etapa Conferência e declarações

Antes de enviar, revise todos os dados e marque obrigatoriamente:

1. aceite do Regulamento;
2. aceite da Política de Privacidade;
3. declaração de aptidão para participar da prova;
4. confirmação de que as informações prestadas são verdadeiras.

O Regulamento e a Política de Privacidade podem ser abertos no próprio formulário. Para o Regulamento, a Política de Privacidade e a confirmação dos dados, o sistema registra data e hora, versão quando aplicável, endereço IP e identificação do navegador. Para kits especiais, a ciência das regras do kit também é registrada. A declaração de aptidão é obrigatória para concluir o envio, mas não possui uma trilha de aceite separada no cadastro. Os registros existentes não devem ser alterados manualmente no painel.

### 7.9. Envio, protocolo e pagamento

Ao clicar em **Enviar inscrição**, o servidor valida novamente documentos, idade, duplicidade, disponibilidade, limites, código de indicação e aceites. Se houver erro, a inscrição não é concluída; corrija os campos indicados e envie novamente.

Quando os dados são válidos, o sistema cria a inscrição com um protocolo e pagamento inicialmente **Pendente**.

- Se o kit for gratuito, ou se o checkout não estiver configurado, o participante permanece na página e recebe a mensagem de sucesso com o protocolo.
- Se o kit tiver valor e o Asaas estiver ativo e configurado, o participante é redirecionado ao checkout externo para pagar por uma das formas habilitadas.
- Se o checkout for cancelado ou expirar, a inscrição continua registrada como **Pendente**.
- O retorno de sucesso ou o webhook autenticado do Asaas atualiza o pagamento para **Pago**. O aviso de retorno sem identificação assinada apenas informa que a conciliação automática ainda ocorrerá.
- Se o Asaas não conseguir criar o checkout, o sistema mantém a inscrição já gravada e exibe uma orientação para revisar os dados do pagador. Evite enviar o formulário repetidamente, pois o CPF já estará vinculado à inscrição criada.

Após o registro, o atleta recebe um e-mail com seu nome, protocolo, prova, situação do pagamento e um link assinado para consultar a própria inscrição. CPF, endereço, dados de contato e informações de saúde não são reproduzidos nesse e-mail. Mudanças administrativas relevantes e cancelamentos também geram e-mail de atualização.

### 7.10. Revisão no painel administrativo

Em **Inscrições**, use a busca e os filtros para localizar o atleta. O protocolo, os dados do gateway, o link de checkout e os registros de aceite da Política de Privacidade são somente leitura.

Ao revisar ou corrigir uma inscrição:

- confira nome, nascimento, sexo, CPF, telefone e e-mail;
- para menor de idade, confira nome e CPF do responsável e a indicação de preenchimento pelo representante legal;
- confira os dados do pagador antes de investigar erros de checkout;
- confirme se prova, kit, tamanho de camisa e código de indicação correspondem ao solicitado;
- ajuste o pagamento para Pendente, Pago ou Cancelado somente após confirmar a situação real;
- use **Cancelar inscrição** quando quiser cancelar e enviar ao atleta o e-mail de atualização;
- registre observações, contato de emergência e informações de saúde somente quando necessários à operação e à segurança do evento.

Não crie uma segunda inscrição para contornar pagamento pendente ou erro de checkout. Primeiro localize a inscrição existente pelo CPF ou protocolo e verifique o status e a referência do gateway.

### 7.11. Exportação e entrega de kits

Os campos de saúde, CPF, contato, endereço e responsável são dados pessoais ou sensíveis. Evite copiá-los para planilhas, mensagens ou observações sem necessidade. A exportação contém apenas colunas operacionais aprovadas.

A ação **Lista de entrega de kits** inclui somente inscrições pagas, agrupadas por kit, com tamanho de camisa, acréscimos conquistados pelo desbravador e espaço para assinatura. Gere o arquivo apenas quando necessário, restrinja seu acesso e descarte cópias conforme a política de retenção do evento.

### 7.12. Solução de problemas comuns

- **“Este atleta já possui uma inscrição”:** procure o CPF no painel; pode existir uma inscrição com pagamento pendente.
- **Prova indisponível para a idade:** confira a data de nascimento e a faixa etária configurada na prova.
- **Prova ou kit esgotado:** confirme os limites e inscrições não canceladas antes de ampliar vagas.
- **Código de desbravador inválido:** confira os quatro dígitos, se o cadastro está ativo e se o código já foi usado em um kit Desbravador.
- **Checkout não abriu:** revise CPF/CNPJ, nome completo e endereço do pagador, depois confira ambiente, chave e configuração do Asaas.
- **Pagamento ainda pendente após o checkout:** aguarde a conciliação por webhook e confira o gateway antes de alterar o status manualmente.
- **E-mail não recebido:** confirme o endereço cadastrado e a configuração de e-mail da aplicação; não envie dados sensíveis por canais alternativos.

## 8. Resultados e rankings

Em **Resultados e rankings**, localize o atleta e clique em **Informar resultado**.

- **Número do peito:** obrigatório e não pode se repetir.
- **Situação:** escolha Aguardando, Concluiu, Não largou, Não concluiu ou Desclassificado.
- **Tempo de corrida:** obrigatório para quem concluiu; use `HH:MM:SS`, por exemplo `01:23:45`.
- **Categoria:** é calculada na inscrição a partir do sexo e da idade na data da prova (ou na data geral do evento quando a prova não tiver data). Pode ser corrigida entre as faixas de 6–7 até 60+, separadas por sexo.

Ao salvar, o sistema recalcula automaticamente as posições geral, por sexo e por categoria. Não digite classificações manualmente sem necessidade.

## 9. Mensagens de contato

As mensagens normalmente chegam pelo formulário público. Em **Mensagens de contato**, confira nome, e-mail, telefone, assunto e mensagem, e use **Lida em** para registrar o atendimento.

Não altere o conteúdo enviado pelo usuário, exceto quando houver uma necessidade operacional clara. Não reutilize os dados de contato para divulgação sem base legal ou consentimento adequado.

## Checklist antes de abrir inscrições

- dados, prazo, limite, contato e regulamento do evento revisados;
- todas as provas com faixa etária, limite e ordem corretos;
- kits com preço final, foto e disponibilidade conferidos;
- quantidade máxima de cada kit e tamanhos de camisa conferidos;
- desbravadores ativos, códigos distribuídos e níveis de upgrade revisados, quando aplicável;
- checkout testado no ambiente correto;
- webhook do Asaas configurado com o mesmo token da aplicação e testado;
- usuários administrativos individuais criados;
- página pública e formulário de inscrição conferidos em computador e celular;
- Política de Privacidade, regulamento e declaração de aptidão atualizados e com aceite obrigatório.
