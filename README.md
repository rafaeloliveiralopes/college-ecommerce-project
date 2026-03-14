# E-commerce com CMS em PHP

Aplicação web de e-commerce desenvolvida em PHP com painel administrativo, autenticação de clientes, carrinho, checkout e integração de pagamento via PayPal Sandbox.

O projeto consolida as entregas previstas no roteiro acadêmico descrito em `rap.pdf`, cobrindo as atividades práticas 1 a 8 em uma única base de código.

## Demo Online

- Loja publicada: `http://shopcmsrafaellopes.infinityfree.me`
- Painel administrativo: `http://shopcmsrafaellopes.infinityfree.me/admin/login.php`

## Visão Geral

Este repositório entrega uma loja virtual com duas frentes principais:

- área pública para navegação, catálogo, carrinho, checkout e acompanhamento de pedidos;
- painel administrativo para autenticação, consulta de pedidos, gestão de produtos e visualização de contas.

O banco `project_db.sql` inclui a estrutura principal da aplicação e dados iniciais para demonstração local.

## Funcionalidades

- Home page com layout modular em PHP, header e footer reutilizáveis.
- Catálogo público de produtos com listagem e página de detalhes.
- Cadastro, login e área autenticada do cliente.
- Carrinho com atualização de quantidade, remoção de itens e cálculo de total.
- Checkout com gravação de pedidos e itens no banco de dados.
- Consulta de pedidos e tela de detalhes com opção de pagamento.
- Integração com PayPal Sandbox para conclusão de pedidos pendentes.
- Painel admin com login, listagem de pedidos, edição de status e gestão de produtos.

## Stack

- PHP 7.4+
- MySQL
- Apache
- Bootstrap 5
- Font Awesome

## Estrutura

```text
.
├── admin/                # Painel administrativo
├── assets/               # CSS, imagens e recursos visuais
├── layouts/              # Header e footer compartilhados
├── server/               # Conexão e endpoints de pedidos/pagamento
├── account.php           # Área do cliente
├── cart.php              # Carrinho
├── checkout.php          # Finalização do pedido
├── index.php             # Home
├── login.php             # Login do cliente
├── order_details.php     # Detalhes do pedido
├── payments.php          # Pagamento com PayPal
├── products.php          # Catálogo
├── project_db.sql        # Schema e dados iniciais
├── register.php          # Cadastro do cliente
└── single_product.php    # Detalhe do produto
```

## Como Executar

### 1. Preparar o ambiente

Tenha Apache, PHP e MySQL disponíveis localmente. O fluxo mais simples é utilizar XAMPP ou equivalente.

Coloque o projeto dentro do diretório servido pelo Apache, por exemplo:

```text
htdocs/<nome-do-projeto>
```

### 2. Importar o banco

Importe o arquivo `project_db.sql` no MySQL/MariaDB. Ele cria:

- banco `project_db`;
- tabelas `admins`, `products`, `users`, `orders`, `order_items` e `payments`;
- dados iniciais para testes locais.

### 3. Configurar o arquivo `.env`

Crie ou ajuste o arquivo `.env` na raiz do projeto com os valores do seu ambiente:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=project_db
PAYPAL_CLIENT_ID=
PAYPAL_CURRENCY=BRL
```

`PAYPAL_CLIENT_ID` é necessário apenas para testar o fluxo de pagamento no Sandbox do PayPal.

### 4. Acessar a aplicação

Depois de iniciar Apache e MySQL, abra:

```text
http://localhost/<nome-do-projeto>/index.php
```

Painel administrativo:

```text
http://localhost/<nome-do-projeto>/admin/login.php
```

## Publicação no InfinityFree

Deploy publicado em:

```text
http://shopcmsrafaellopes.infinityfree.me
```

Login do painel administrativo:

```text
http://shopcmsrafaellopes.infinityfree.me/admin/login.php
```

## Credenciais de Demonstração

As credenciais abaixo existem para o banco importado no ambiente local e também para a publicação demonstrativa no InfinityFree.

```text
Administrador:

- usuário: `admin`
- e-mail: `admin@shop.com.br`
- senha: `123456`

Cliente de exemplo:

- e-mail: `mariana@example.com`
- senha: `123456`
```

## Observações

- O projeto usa dados seed para facilitar testes locais.
- O pagamento depende de uma conta e de um app Sandbox no PayPal Developer.
- Bibliotecas front-end são carregadas via CDN.
- Para uso fora de ambiente acadêmico ou demonstrativo, revise credenciais, hashing de senha e configurações de segurança.

## Licença

Este projeto está licenciado sob a licença MIT. Consulte `LICENSE` para mais detalhes.
