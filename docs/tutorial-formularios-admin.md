# Tutorial dos formulários administrativos

Este guia explica como preparar e operar cada formulário do painel da Ave Branca Run. Acesse `/admin` com um usuário administrativo. Para o fluxo preenchido pelo atleta, consulte o [Tutorial das inscrições da prova](tutorial-inscricoes-da-prova.md).

Em todos os formulários, campos marcados com `*` são obrigatórios. Campos desativados existem para consulta e não são alterados ao salvar.

## Ordem de configuração inicial

Configure o sistema nesta ordem para que as opções necessárias estejam disponíveis nas inscrições:

1. **Configurações > Evento**
2. **Configurações > Provas**
3. **Configurações > Kits**
4. **Secretaria > Desbravadores**, quando houver pacote exclusivo
5. **Configurações > Camisetas**, quando houver venda avulsa
6. **Configurações > Pagamento**
7. **Configurações > E-mail**
8. **Configurações > Usuários administrativos**

Depois disso, use **Inscrições**, **Resultados e rankings** e **Mensagens de contato** na operação diária.

## 1. Evento

Em **Configurações > Evento**, abra o registro existente ou crie um.

### Dados gerais

- **Data:** texto público usado para comunicar quando ocorrerá o evento. Enquanto não estiver definida, use “A confirmar”. Quando puder ser interpretada como data, também serve de referência etária se a prova não tiver data própria.
- **Local:** texto público que orienta onde ocorrerá o evento e pode servir de cidade de fallback do Pix.
- **Prazo máximo para inscrição:** bloqueia novos envios depois da data e hora escolhidas; não cancela inscrições existentes.
- **Limite total de inscrições:** protege a capacidade global do evento. Aceita inteiro maior que zero ou vazio para não limitar e conta inscrições não canceladas.
- **Razão social do organizador:** identifica juridicamente o controlador dos dados e pode compor o nome do recebedor Pix quando não houver outro.
- **CNPJ do organizador:** identifica o responsável no rodapé e na Política de Privacidade. Use `00.000.000/0000-00`.
- **E-mail de contato:** recebe ou orienta solicitações operacionais e de direitos LGPD.
- **Telefone do evento:** canal público para ligações.
- **WhatsApp do evento:** canal público para atendimento por mensagem.

### Informações da prova

Todos são editores de conteúdo público:

- **Informações gerais:** concentra orientações que não pertencem a uma prova específica.
- **Retirada de pacote:** informa local, período, documentos e quem pode retirar.
- **Guarda-volumes:** declara existência, horários, limitações e responsabilidade.
- **Cronometragem:** explica chip, tempo oficial, publicação e contestação.
- **Inscrições especiais:** explica PCD, cortesias e necessidades específicas sem substituir as regras de cada pacote.
- **Regulamento:** contém as regras contratuais aceitas na inscrição. Cada aceite guarda um resumo criptográfico do texto vigente, por isso revise antes de abrir as inscrições e evite alterações silenciosas.

Use títulos e listas para leitura em celular. Informações específicas de um percurso devem ficar na respectiva prova; condições comerciais de um pacote devem ficar nas regras do pacote.

## 2. Provas

Em **Configurações > Provas**, cadastre uma modalidade para cada percurso ou categoria oferecida.

- **Nome:** título público e opção selecionada na inscrição; deve distinguir claramente o percurso.
- **Categoria:** classifica a apresentação como Infantil, Juvenil, Adulto, Master ou PCD. A categoria não substitui a validação das idades.
- **Distância:** texto público, como `6 km`; não entra no cálculo de resultado.
- **Descrição curta:** resumo exibido na apresentação pública para ajudar a escolha.
- **Idade mínima:** menor idade aceita na data de referência. Vazio significa sem mínimo.
- **Idade máxima:** maior idade aceita e nunca pode ser menor que a mínima. Vazio significa sem máximo.
- **Limite de atletas:** capacidade exclusiva da prova; aceita inteiro maior que zero ou vazio e conta inscrições não canceladas.
- **Ordem de exibição:** inteiro a partir de zero; números menores aparecem primeiro e também pode ser ajustada por arraste na lista.
- **Disponível para inscrições:** controla novas escolhas. Desativar preserva inscrições existentes.
- **Data da prova:** informa o dia e tem prioridade no cálculo da idade e categoria.
- **Horário da largada:** informação pública; os segundos não são usados.
- **URL de incorporação do Google Maps:** permite exibir o mapa. Cole o endereço de **Compartilhar > Incorporar um mapa**, não a URL comum de navegação.
- **Informações do percurso:** editor para trajeto, hidratação, altimetria e avisos.
- **Fotos do percurso:** até 6 imagens públicas de 4 MB cada; a ordem pode ser alterada por arraste.

Salve uma prova antes de tentar associá-la a uma inscrição.

## 3. Kits

Em **Configurações > Kits**, cadastre cada pacote disponível.

- **Nome:** identifica o pacote para o atleta e nas listas de entrega.
- **Foto:** imagem pública do conteúdo; use uma foto que não prometa itens fora da descrição.
- **Valor:** preço-base obrigatório e não negativo, usado no total da inscrição.
- **Tipo:** Normal, PCD / 60+, Social ou Desbravador. Tipos diferentes de Normal exigem ciência das regras.
- **Regras exibidas no modal:** condições que o atleta deve ler e aceitar, como comprovação ou contrapartida. O aceite guarda data, IP, navegador e resumo do texto.
- **Quantidade máxima:** estoque do pacote contado por inscrições não canceladas; vazio significa ilimitado.
- **Inclui camiseta:** determina se tamanho é mostrado, obrigatório e salvo. Desative para pacotes sem camiseta.
- **Ordem:** controla a posição pública; use inteiro a partir de zero.
- **Ativo:** permite novas seleções sem apagar o histórico.
- **Descrição:** até 1.000 caracteres para explicar itens e diferenciais.

Um kit gratuito deve ter valor `0`. O valor cadastrado é sempre o preço final: o checkout não aplica desconto adicional para PCD, idosos ou kit social. Revise preço, regras e estoque antes de ativar o checkout.

## 4. Desbravadores

Em **Secretaria > Desbravadores**, cadastre cada participante habilitado ao pacote exclusivo.

- **Nome:** identifica o desbravador no painel.
- **CPF:** somente os 11 dígitos, único no cadastro. O sistema compara esse CPF ao CPF do atleta que escolhe o pacote.
- **Ativo:** habilita ou bloqueia novas inscrições desse desbravador sem apagar o cadastro.

No fluxo atual não existe código de quatro dígitos nem indicação digitada pelo participante. O pacote **Desbravador** exige que o CPF do próprio atleta esteja ativo nesta lista e ainda não esteja ligado a uma inscrição. Cada desbravador pode possuir uma única inscrição.

## 5. Camisetas e itens avulsos

Em **Configurações > Camisetas**, cadastre produtos que podem ser vendidos na loja e, quando ativos, adicionados à inscrição.

- **Nome:** identifica o item na vitrine, na inscrição e nos pedidos.
- **Descrição:** até 2.000 caracteres para material, modelo e orientações.
- **Foto:** imagem pública usada para reconhecer o produto.
- **Valor na loja:** preço obrigatório e não negativo para compra separada.
- **Valor junto da inscrição:** preço promocional opcional. Vazio reaproveita o valor da loja; zero torna o adicional gratuito.
- **Estoque:** quantidade total disponível. Vazio significa ilimitado; zero impede novos pedidos.
- **Ativa:** controla exibição e novos pedidos sem apagar pedidos antigos.

O item adicional não substitui a camiseta do pacote. O preço do item é somado ao total da inscrição e seu status acompanha o pagamento principal.

## 6. Pagamento

Em **Configurações > Pagamento**, escolha Pix manual ou pagamento on-line. Se os dois estiverem configurados, o **Pix manual tem prioridade** nas inscrições com valor.

### Pix manual

- **Ativar Pix manual:** envia o participante à página de Pix e comprovante.
- **Chave Pix:** dado usado no QR Code e copia e cola.
- **Nome do recebedor:** nome do padrão Pix, com até 25 caracteres; deve corresponder ao recebedor esperado no banco.
- **Cidade do recebedor:** cidade do padrão Pix, com até 15 caracteres.
- **Banco, Agência, Conta e Titular:** dados exibidos para o pagador conferir antes da transferência. A conta deve incluir dígito quando houver.

Com Pix manual ativo, teste o QR Code, o valor total, a chave e o armazenamento privado do comprovante. O envio muda o status para **Em análise**, não para Pago.

### Pagamento on-line

1. Deixe **Ativar pagamento on-line** desligado durante a configuração.
2. Em **Provedor**, escolha Asaas; o campo identifica a integração utilizada.
3. Em **Ambiente**, use Sandbox para teste e Produção somente depois da homologação.
4. Em **Chave da API**, cole a chave do mesmo ambiente. Ela cria o checkout e fica criptografada no banco.
5. Em **Expiração em minutos**, informe de 10 a 1.440; esse período controla a validade da sessão.
6. Em **Meios de pagamento**, habilite Pix e/ou cartão conforme o que será oferecido.
7. Em **Tipo de cobrança**, selecione ao menos uma opção. Para a cobrança única da inscrição, use **Avulsa**.
8. Salve, faça uma inscrição de teste e, somente após validar o fluxo, ative o pagamento on-line.

A API key fica criptografada no banco. Não a compartilhe, não a inclua em capturas de tela e não altere a `APP_KEY` sem um procedimento de rotação. A chave de Sandbox não funciona em Produção e vice-versa.

Além da API key, configure `ASAAS_WEBHOOK_TOKEN` no ambiente da aplicação e cadastre o mesmo segredo no webhook do Asaas. Requisições sem o cabeçalho `asaas-access-token` correto são rejeitadas.

## 7. E-mail

Em **Configurações > E-mail**, configure as mensagens transacionais.

- **Método de envio:** `Log` não envia e é adequado para desenvolvimento; `SMTP` entrega ao servidor configurado.
- **Segurança:** Automática/STARTTLS negocia criptografia; SSL/TLS inicia a conexão segura diretamente. Use a opção indicada pelo provedor.
- **Servidor SMTP:** hostname fornecido pelo serviço de e-mail.
- **Porta:** inteiro de 1 a 65.535 compatível com a segurança escolhida.
- **Usuário:** login SMTP, quando exigido.
- **Senha:** credencial criptografada. Ao editar, deixe vazia para manter a atual.
- **E-mail do remetente:** endereço que aparece como origem e deve estar autorizado no provedor.
- **Nome do remetente:** identificação legível, normalmente Ave Branca Run.

Teste recebimento, spam e links antes de abrir inscrições. Não use e-mail para reproduzir CPF, endereço ou comprovante.

## 8. Usuários administrativos

Em **Configurações > Usuários administrativos**, informe nome, e-mail único, senha e confirmação.

A senha deve ter ao menos 12 caracteres e conter letras maiúsculas e minúsculas, números e símbolos. Ao editar um usuário, deixe os campos de senha vazios para conservar a senha atual. Crie contas individuais; não compartilhe credenciais entre operadores.

## 9. Inscrições

As inscrições normalmente são criadas pelo formulário público em `/inscricao` e revisadas no painel em **Inscrições**. Antes de divulgar o formulário, confirme que o evento está dentro do prazo, que o limite geral ainda não foi atingido e que existe pelo menos uma prova e um kit ativos.

### 9.1. Antes de começar

Oriente o participante a separar:

- dados pessoais e de contato do atleta;
- CPF do atleta, pois cada CPF pode possuir somente uma inscrição;
- nome e CPF do responsável legal, quando o atleta for menor de 18 anos ou não puder responder por si;
- dados do pagador exigidos pelo fluxo de pagamento;
- telefone de um contato de emergência, quando houver;
- CPF ativo no cadastro de desbravadores, quando esse pacote for usado.

Máscaras como pontos, traços e parênteses ajudam no preenchimento, mas o sistema valida os números. Um CPF ou CNPJ com quantidade correta de dígitos, porém matematicamente inválido, não é aceito.

### 9.2. Etapa Atleta

Preencha o nome completo, CPF, data de nascimento, sexo, telefone e e-mail do atleta.

- A data de nascimento deve ser anterior ao dia atual e será usada para liberar somente provas compatíveis com a idade na data de referência do evento.
- O CPF deve ser válido e não pode estar associado a outra inscrição, inclusive uma inscrição ainda pendente de pagamento.
- O telefone deve ter DDD e 10 ou 11 dígitos.
- O e-mail deve estar correto, pois recebe o protocolo, o status e o link assinado para a página do atleta.
- Marque **Estou preenchendo como representante legal** em casos de menoridade, tutela, curatela ou impossibilidade do titular responder por si.

Quando a data de nascimento identificar um menor de 18 anos, o sistema considera automaticamente que o formulário foi preenchido por representante legal.

### 9.3. Etapa Responsável Legal

Esta etapa aparece quando há representação legal. Informe nome completo e CPF válido do responsável. Para menores de idade, os dois campos são obrigatórios mesmo que a caixa de representação não tenha sido marcada manualmente.

Esses dados servem para registrar a autorização e a responsabilidade pela inscrição do menor. Não use os dados do responsável como substitutos dos dados do atleta: cada pessoa deve permanecer em seu campo próprio.

### 9.4. Etapa Pagador

Informe os dados da pessoa física ou jurídica responsável pelo pagamento:

- nome completo ou razão social;
- CPF com 11 dígitos ou CNPJ com 14 dígitos;
- rua, avenida ou travessa;
- número do endereço;
- bairro;
- CEP com 8 dígitos.

Esses campos só são obrigatórios no formulário inicial quando o checkout on-line estiver configurado. Eles são enviados ao Asaas no limite necessário para criar e reconciliar a cobrança. No Pix manual, nome e CPF do pagador são informados junto com o comprovante.

### 9.5. Etapa Prova

Após informar a data de nascimento, escolha uma das provas exibidas como disponíveis.

O formulário pode impedir a seleção quando:

- a prova está inativa;
- a idade do atleta não pertence à faixa configurada;
- o limite de participantes da prova foi atingido;
- o prazo geral de inscrições terminou;
- o limite total do evento foi atingido.

A conferência é repetida no envio do formulário. Assim, se a última vaga for ocupada por outra pessoa enquanto o participante preenche os dados, o sistema não ultrapassa o limite: ele retorna uma mensagem e solicita uma nova escolha.

### 9.6. Etapa Kit

Selecione o kit, confira o preço final e escolha o tamanho da camisa entre PP, P, M, G, GG e XGG. A disponibilidade é verificada novamente ao enviar, portanto um kit esgotado não será confirmado mesmo que ainda estivesse visível quando a página foi aberta.

As regras variam conforme o tipo:

- **Normal:** não exige regra adicional;
- **PCD / 60+:** exige leitura e confirmação das regras próprias; eventual comprovação ocorre conforme o texto configurado para o kit;
- **Kit Social:** exige leitura e confirmação das contrapartidas configuradas;
- **Desbravador:** exige que o CPF do atleta esteja ativo no cadastro de desbravadores e sem outra inscrição.

Ao escolher um kit especial, o sistema abre um modal. Leia o conteúdo, marque **Li e estou ciente das regras deste kit** e confirme. Sem esse aceite, a inscrição não pode ser concluída. O valor apresentado no kit já é o preço final; não existe desconto adicional no checkout.

### 9.7. Item adicional e emergência

Os campos são opcionais até que uma escolha os torne obrigatórios:

- **Item avulso:** adiciona uma camiseta/produto ativo ao total.
- **Tamanho adicional:** obrigatório ao escolher item.
- **Quantidade adicional:** obrigatória, de 1 a 10 e limitada pelo estoque.
- **Contato de emergência:** nome e telefone de quem deve ser acionado.

### 9.8. Etapa Conferência e declarações

Antes de enviar, revise todos os dados e marque obrigatoriamente:

1. aceite do Regulamento;
2. aceite da Política de Privacidade;
3. declaração de aptidão para participar da prova;
4. confirmação de que as informações prestadas são verdadeiras.

O Regulamento e a Política de Privacidade podem ser abertos no próprio formulário. Para o Regulamento, a Política de Privacidade e a confirmação dos dados, o sistema registra data e hora, versão quando aplicável, endereço IP e identificação do navegador. Para kits especiais, a ciência das regras do kit também é registrada. A declaração de aptidão é obrigatória para concluir o envio, mas não possui uma trilha de aceite separada no cadastro. Os registros existentes não devem ser alterados manualmente no painel.

### 9.9. Envio, protocolo e pagamento

Ao clicar em **Enviar inscrição**, o servidor valida novamente documentos, idade, duplicidade, disponibilidade, limites, elegibilidade do desbravador, estoque do item e aceites. Se houver erro, a inscrição não é concluída; corrija os campos indicados e envie novamente.

Quando os dados são válidos, o sistema cria a inscrição com um protocolo e pagamento inicialmente **Pendente**.

- Se o kit tiver valor e Pix manual estiver ativo, o participante recebe um link assinado válido por sete dias, vê o QR Code e envia o comprovante. O status muda para **Em análise**.
- Se o Pix manual estiver inativo, o kit tiver valor e o Asaas estiver configurado, o participante é redirecionado ao checkout.
- Se o kit for gratuito ou não houver pagamento configurado, a inscrição permanece **Pendente** até ajuste administrativo.
- Se o checkout for cancelado ou expirar, a inscrição continua registrada como **Pendente**.
- O retorno de sucesso ou o webhook autenticado do Asaas atualiza o pagamento para **Pago**. O aviso de retorno sem identificação assinada apenas informa que a conciliação automática ainda ocorrerá.
- Se o Asaas não conseguir criar o checkout, o sistema mantém a inscrição já gravada e exibe uma orientação para revisar os dados do pagador. Evite enviar o formulário repetidamente, pois o CPF já estará vinculado à inscrição criada.

Após o registro, o atleta recebe um e-mail com seu nome, protocolo, prova, situação do pagamento e um link assinado para consultar a própria inscrição. CPF, endereço, dados de contato e informações de emergência não são reproduzidos nesse e-mail. Mudanças administrativas relevantes e cancelamentos também geram e-mail de atualização.

### 9.10. Revisão no painel administrativo

Em **Inscrições**, use a busca e os filtros para localizar o atleta. O protocolo, os dados do gateway, o link de checkout e os registros de aceite da Política de Privacidade são somente leitura.

Os campos administrativos complementam os dados já explicados nas etapas públicas:

- **Protocolo:** identificador automático usado no atendimento; somente leitura.
- **Número de peito:** identifica o atleta na prova e deve ser único.
- **Status do pagamento:** Pendente, Em análise, Pago ou Cancelado; controla confirmação, limites e o status de itens vinculados.
- **Gateway:** informa qual integração criou a cobrança; somente leitura.
- **Referência do gateway:** chave de conciliação com o provedor; somente leitura.
- **Link do checkout:** URL externa criada para aquela cobrança; somente leitura.
- **Comprovante do Pix:** arquivo privado enviado pelo pagador; disponível apenas para abrir ou baixar durante a conferência.
- **Status na prova:** Aguardando, Concluiu, Não largou, Não concluiu ou Desclassificado.
- **Tempo oficial:** duração `HH:MM:SS`; necessário para classificar quem concluiu.
- **Categoria do resultado:** faixa por sexo e idade usada no ranking.
- **Classificação geral, por sexo e na categoria:** posições calculadas; altere manualmente apenas para correção justificada.
- **Versão e data do aceite da política:** trilha histórica somente leitura que prova qual texto foi apresentado e quando.

Ao revisar ou corrigir uma inscrição:

- confira nome, nascimento, sexo, CPF, telefone e e-mail;
- para menor de idade, confira nome e CPF do responsável e a indicação de preenchimento pelo representante legal;
- confira os dados do pagador antes de investigar erros de checkout;
- confirme se prova, pacote, tamanho de camiseta, item adicional e vínculo de desbravador correspondem ao solicitado;
- ajuste o pagamento para Pendente, Pago ou Cancelado somente após confirmar a situação real;
- use **Cancelar inscrição** quando quiser cancelar e enviar ao atleta o e-mail de atualização;
- use o comprovante Pix somente para conciliação e mantenha o arquivo em área restrita;
- confira o contato de emergência somente quando necessário à segurança.

Não crie uma segunda inscrição para contornar pagamento pendente ou erro de checkout. Primeiro localize a inscrição existente pelo CPF ou protocolo e verifique o status e a referência do gateway.

### 9.11. Exportação e entrega de kits

CPF, contato, endereço, responsável e comprovante são dados pessoais. Evite copiá-los para planilhas ou mensagens sem necessidade. A exportação contém apenas colunas operacionais aprovadas.

A ação **Lista de entrega de kits** inclui somente inscrições pagas, agrupadas por kit, com tamanho de camisa, acréscimos conquistados pelo desbravador e espaço para assinatura. Gere o arquivo apenas quando necessário, restrinja seu acesso e descarte cópias conforme a política de retenção do evento.

### 9.12. Solução de problemas comuns

- **“Este atleta já possui uma inscrição”:** procure o CPF no painel; pode existir uma inscrição com pagamento pendente.
- **Prova indisponível para a idade:** confira a data de nascimento e a faixa etária configurada na prova.
- **Prova ou kit esgotado:** confirme os limites e inscrições não canceladas antes de ampliar vagas.
- **Desbravador não habilitado:** confira se o CPF do atleta está ativo no cadastro e sem inscrição vinculada.
- **Checkout não abriu:** revise CPF/CNPJ, nome completo e endereço do pagador, depois confira ambiente, chave e configuração do Asaas.
- **Pagamento ainda pendente após o checkout:** aguarde a conciliação por webhook e confira o gateway antes de alterar o status manualmente.
- **E-mail não recebido:** confirme o endereço cadastrado e a configuração de e-mail da aplicação; não envie dados sensíveis por canais alternativos.

## 10. Pedidos de itens avulsos

Em **Pedidos de itens**, cada campo existe para conciliação:

- **Item avulso:** produto vendido.
- **Inscrição:** vínculo opcional; quando preenchido, permite consultar o comprovante e sincroniza o status.
- **Nome, e-mail e telefone do cliente:** identificam e permitem comunicar sobre o pedido.
- **Tamanho e quantidade:** definem o que separar.
- **Valor unitário e total:** registram o preço aplicado; o total deve corresponder a valor unitário × quantidade.
- **Status do pagamento:** Pendente, Em análise, Pago ou Cancelado.
- **Comprovante do Pix:** somente leitura e vindo da inscrição vinculada; não faça upload por este formulário.

Corrija valores apenas com justificativa operacional e nunca use uma inscrição de outra pessoa para acessar comprovante.

## 11. Resultados e rankings

Em **Resultados e rankings**, localize o atleta e clique em **Informar resultado**.

- **Número do peito:** obrigatório e não pode se repetir.
- **Situação:** escolha Aguardando, Concluiu, Não largou, Não concluiu ou Desclassificado.
- **Tempo de corrida:** obrigatório para quem concluiu; use `HH:MM:SS`, por exemplo `01:23:45`.
- **Categoria:** é calculada na inscrição a partir do sexo e da idade na data da prova (ou na data geral do evento quando a prova não tiver data). Pode ser corrigida entre as faixas de 6–7 até 60+, separadas por sexo.

Ao salvar, o sistema recalcula automaticamente as posições geral, por sexo e por categoria. Não digite classificações manualmente sem necessidade.

## 12. Mensagens de contato

As mensagens normalmente chegam pelo formulário público:

- **Nome:** identifica quem solicita atendimento.
- **E-mail:** canal obrigatório para resposta.
- **Telefone:** canal alternativo opcional.
- **Assunto:** resumo opcional usado na triagem.
- **Mensagem:** conteúdo obrigatório, com até 2.000 caracteres.
- **Lida em:** data e hora interna que registra a triagem; não altera a mensagem.

Não altere o conteúdo enviado pelo usuário, exceto quando houver uma necessidade operacional clara. Não reutilize os dados de contato para divulgação sem base legal ou consentimento adequado.

## Checklist antes de abrir inscrições

- dados, prazo, limite, contato e regulamento do evento revisados;
- todas as provas com faixa etária, limite e ordem corretos;
- kits com preço final, foto e disponibilidade conferidos;
- quantidade máxima de cada kit e tamanhos de camisa conferidos;
- desbravadores ativos e CPFs habilitados conferidos, quando aplicável;
- checkout testado no ambiente correto;
- webhook do Asaas configurado com o mesmo token da aplicação e testado;
- usuários administrativos individuais criados;
- página pública e formulário de inscrição conferidos em computador e celular;
- Política de Privacidade, regulamento e declaração de aptidão atualizados e com aceite obrigatório.
