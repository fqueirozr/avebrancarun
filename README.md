# Ave Branca Run

Sistema de inscrições, pagamentos e gestão da **Ave Branca Run**, desenvolvido em Laravel. A aplicação reúne site público, inscrição de atletas, Pix manual com comprovante, checkout opcional pelo Asaas, página individual do atleta, loja de itens avulsos e painel administrativo.

## Tecnologias

- PHP 8.3
- Laravel 13
- Filament 5 (painel administrativo)
- Livewire 4
- MySQL
- Tailwind CSS 4 e Vite 8
- Pest 4
- Asaas (checkout e confirmação de pagamento)

## Funcionalidades principais

- Página pública com informações do evento, modalidades e kits;
- inscrição de atletas adultos e menores, com aceite dos termos obrigatórios;
- escolha de tamanho de camiseta conforme o pacote e controle de limite por evento, prova, pacote e estoque;
- inclusão opcional de camiseta/item avulso na inscrição;
- Pix manual com QR Code, copia e cola, comprovante privado e análise administrativa;
- checkout Asaas opcional via Pix e cartão de crédito;
- atualização automática do pagamento por webhook autenticado;
- e-mails de recebimento e atualização da inscrição;
- página do atleta acessada por URL assinada;
- categorias etárias e rankings geral, por sexo e por categoria;
- elegibilidade de desbravadores por CPF previamente habilitado;
- painel administrativo em `/admin`;
- gestão de inscrições, resultados, provas, pacotes, camisetas, pedidos, desbravadores, contatos, e-mail e pagamento;
- exportação das inscrições e impressão da lista de entrega de kits pagos, com campo de assinatura.

## Guia do painel administrativo

Depois de criar o primeiro usuário com `php artisan make:filament-user`, acesse `/admin`. Para colocar um evento em operação, configure o painel nesta ordem:

1. dados do evento;
2. provas e faixas etárias;
3. kits e preços;
4. desbravadores, quando o pacote correspondente for utilizado;
5. camisetas/itens avulsos;
6. Pix manual ou gateway de pagamento;
7. envio de e-mails;
8. usuários administrativos.

As inscrições dependem de uma prova e de um pacote ativos. Consulte:

- [Tutorial dos formulários administrativos](docs/tutorial-formularios-admin.md), com a finalidade e a regra de cada campo do painel;
- [Tutorial das inscrições da prova](docs/tutorial-inscricoes-da-prova.md), com todas as etapas, validações, status e regras para o participante;
- [Política de Privacidade](docs/politica-de-privacidade.md), versão integral correspondente ao texto apresentado no formulário.

## Requisitos

- PHP 8.3 ou superior, com as extensões exigidas pelo Laravel e pelo driver MySQL;
- Composer 2;
- Node.js e npm;
- MySQL 8 ou compatível;
- servidor web apontando o document root para `public/` em produção.

## Instalação local

1. Clone o repositório e entre na pasta do projeto.

2. Instale as dependências PHP e crie o arquivo de ambiente:

```bash
composer install
copy .env.example .env
php artisan key:generate
```

No Linux ou macOS, substitua `copy` por `cp`.

3. Crie o banco MySQL e ajuste o `.env` antes de executar as migrations:

```dotenv
APP_NAME="Ave Branca Run"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=runapp
DB_USERNAME=root
DB_PASSWORD=
```

4. Prepare o banco e os assets:

```bash
php artisan migrate --seed
npm install
npm run build
```

> Os seeders criam configurações iniciais e dados de demonstração, inclusive usuários, inscrições, contatos e desbravadores. Use `php artisan migrate` sem `--seed` quando não quiser dados fictícios e cadastre os dados reais pelo painel. O comando `composer run setup` continua disponível como atalho quando o `.env` e o banco já estiverem preparados.

5. Crie o usuário administrador:

```bash
php artisan make:filament-user
```

6. Crie o link para arquivos públicos:

```bash
php artisan storage:link
```

7. Inicie o ambiente de desenvolvimento:

```bash
composer run dev
```

Esse comando inicia o servidor Laravel, o worker da fila e o Vite. Acesse a URL definida em `APP_URL`; o painel fica em `APP_URL/admin`.

## Configuração do ambiente

### Banco, cache, sessão e fila

Por padrão, o projeto utiliza o banco para sessão, cache e fila:

```dotenv
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

As tabelas necessárias já fazem parte das migrations. Em produção, mantenha um worker ativo sob um gerenciador de processos:

```bash
php artisan queue:work --tries=3
```

Embora o webhook envie o e-mail de confirmação de forma síncrona atualmente, o worker é necessário para recursos enfileirados do Laravel/Filament e futuras notificações.

### E-mail

Em desenvolvimento, `MAIL_MAILER=log` grava os e-mails em `storage/logs/laravel.log`. Para envio real, configure um serviço SMTP, por exemplo:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.exemplo.com
MAIL_PORT=587
MAIL_USERNAME=usuario
MAIL_PASSWORD=senha
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=contato@exemplo.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Pagamento

O painel permite Pix manual ou checkout on-line. Quando o Pix manual está ativo, ele tem prioridade para inscrições com valor: o participante recebe QR Code/copia e cola, informa nome e CPF do pagador e envia comprovante de até 5 MB para análise. O arquivo é salvo no disco privado.

Para checkout on-line, as URLs padrão do Asaas já estão no `.env.example`:

As URLs padrão já estão no `.env.example`:

```dotenv
PAYMENT_GATEWAY=asaas
ASAAS_WEBHOOK_TOKEN=gere-um-segredo-longo-e-aleatorio
ASAAS_SANDBOX_BASE_URL=https://api-sandbox.asaas.com
ASAAS_PRODUCTION_BASE_URL=https://api.asaas.com
ASAAS_SANDBOX_CHECKOUT_URL=https://sandbox.asaas.com/checkoutSession/show
ASAAS_PRODUCTION_CHECKOUT_URL=https://asaas.com/checkoutSession/show
```

A API key **não é configurada no `.env`**. Depois de criar o administrador:

1. Acesse `/admin/payment-gateway-settings`;
2. abra ou crie a configuração do gateway;
3. selecione o ambiente `Sandbox` ou `Produção`;
4. informe a API key gerada no Asaas em **Integrações > API Key**;
5. escolha os meios de pagamento e o tempo de expiração;
6. deixe **Pix manual** desligado se quiser redirecionar ao Asaas;
7. ative **Pagamento on-line** e salve.

A API key é armazenada com cast criptografado. Preserve a mesma `APP_KEY` após começar a utilizar o sistema; trocá-la sem um processo de rotação impedirá a leitura dos dados criptografados.

## Webhook do Asaas

### URL e método

Defina um segredo longo e aleatório em `ASAAS_WEBHOOK_TOKEN`. Cadastre o mesmo valor como token de autenticação do webhook no ambiente correspondente do Asaas e use uma URL HTTPS pública:

```text
POST https://seu-dominio.com/webhooks/asaas
```

Use exatamente o domínio configurado em `APP_URL`. Em desenvolvimento local, o Asaas precisa alcançar a aplicação; exponha-a temporariamente por um túnel HTTPS e cadastre a URL pública gerada.

### Eventos

Habilite os seguintes eventos de cobrança no Asaas:

- `PAYMENT_CONFIRMED`
- `PAYMENT_RECEIVED`
- `PAYMENT_RECEIVED_IN_CASH`

Outros eventos são aceitos pela rota, mas ignorados com resposta HTTP `204 No Content`.

### Funcionamento

O checkout envia ao Asaas a referência externa no formato:

```text
participant-registration:{id}
```

Ao receber um dos eventos de pagamento, a aplicação:

1. identifica a inscrição por `payment.externalReference`;
2. usa a referência da sessão de checkout como alternativa;
3. altera `payment_status` para `paid`;
4. envia o e-mail de atualização ao participante;
5. responde com HTTP `204`.

O processamento é idempotente: uma inscrição que já está paga não é atualizada novamente nem recebe outro e-mail.

### Autenticação e segurança

A rota está fora da validação CSRF, como é necessário para webhooks externos, mas exige que o cabeçalho `asaas-access-token` corresponda a `ASAAS_WEBHOOK_TOKEN`. Token ausente, vazio ou inválido recebe HTTP `401 Unauthorized`; mantenha o segredo fora de logs e do controle de versão.

Não registre payloads completos do webhook: eles podem conter dados pessoais e financeiros. O código atual registra somente referências mínimas quando não consegue associar o pagamento a uma inscrição.

### Teste do webhook

Com uma inscrição existente, um exemplo de payload é:

```json
{
  "event": "PAYMENT_CONFIRMED",
  "payment": {
    "id": "pay_123",
    "externalReference": "participant-registration:1"
  }
}
```

Envio manual para ambiente local:

```bash
curl -X POST http://localhost:8000/webhooks/asaas \
  -H "Content-Type: application/json" \
  -H "asaas-access-token: valor-de-ASAAS_WEBHOOK_TOKEN" \
  -d '{"event":"PAYMENT_CONFIRMED","payment":{"id":"pay_123","externalReference":"participant-registration:1"}}'
```

O endpoint responde `204` inclusive para eventos ignorados ou referências desconhecidas. Para validar o fluxo automatizado:

```bash
php artisan test --compact tests/Feature/AsaasWebhookTest.php
```

## Testes e qualidade

Execute toda a suíte:

```bash
composer test
```

Formate os arquivos PHP:

```bash
vendor/bin/pint --format agent
```

Compile os assets para produção:

```bash
npm run build
```

## Publicação em produção

Checklist mínimo:

- configure `APP_ENV=production`, `APP_DEBUG=false` e uma `APP_URL` HTTPS pública;
- mantenha `APP_KEY` e credenciais somente em variáveis de ambiente/secret manager;
- configure banco, SMTP e permissões de escrita em `storage/` e `bootstrap/cache/`;
- execute `composer install --no-dev --optimize-autoloader` e `npm ci && npm run build`;
- execute `php artisan migrate --force`;
- execute `php artisan optimize` após configurar o ambiente;
- mantenha `php artisan queue:work` sob Supervisor ou serviço equivalente;
- configure o servidor web com raiz em `public/`;
- configure e teste o webhook Asaas de produção;
- monitore a rota de saúde `GET /up` e os logs da aplicação.

Após cada publicação com alterações de código, reinicie os processos de longa duração:

```bash
php artisan reload
```

## Estrutura relevante

```text
app/Filament/                 Painel administrativo
app/Http/Controllers/         Inscrição, contato e webhook
app/Payments/                 Integração e checkout Asaas
database/migrations/          Estrutura do banco
database/seeders/             Dados iniciais e de demonstração
resources/views/              Páginas públicas e e-mails
routes/web.php                Rotas públicas e webhook
tests/Feature/                Testes dos fluxos principais
```

## Privacidade

O sistema trata dados de atletas, responsáveis, pagadores, contatos de emergência, compradores e usuários administrativos. A [Política de Privacidade integral](docs/politica-de-privacidade.md) descreve controlador, categorias de dados, menores, finalidades e bases legais, pagamentos, compartilhamento, divulgação de resultados, retenção, segurança, direitos LGPD, cookies e atualizações.

Na operação:

- restrinja painel, comprovantes, exportações e backups a pessoas autorizadas;
- não envie CPF, endereço, comprovante ou registros técnicos por e-mail;
- compartilhe com Asaas, cronometragem, logística e demais operadores apenas o necessário;
- mantenha a versão da política sincronizada com `ParticipantRegistration::PrivacyPolicyVersion`;
- descarte com segurança listas e exportações após a finalidade;
- preserve os registros de aceite e não os edite manualmente.
