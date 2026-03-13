# Desenvolvimento de E-commerce com CMS

Projeto desenvolvido a partir do roteiro de aula prática da disciplina de Desenvolvimento de E-commerce com CMS.

Até o momento, o repositório contempla a interface inicial da loja virtual, a estrutura de banco de dados do sistema, a continuidade do dashboard administrativo, a vitrine pública de produtos, a autenticação do cliente e o fluxo inicial de carrinho e checkout, seguindo as Aulas Práticas 1, 2, 3, 4, 5, 6 e 7 do roteiro.

---

## Sobre o Projeto

Esta aplicação representa a base inicial de um e-commerce com CMS. O projeto foi organizado com includes em PHP para separar o topo e o rodapé da página, uma folha de estilos própria para personalizar a interface e uma camada inicial de persistência com MySQL.

Atualmente, a página inicial inclui:

- barra de navegacao responsiva com logo, links de navegacao e icones de carrinho e usuario;
- seção principal com banner e chamada promocional;
- rodapé com navegação, contato, destaques e formas de pagamento;
- estilização customizada sobre componentes do Bootstrap.

Além disso, a Aula Prática 2 adiciona:

- script SQL com a criação do banco `project_db`;
- tabelas `admins`, `products`, `users`, `orders`, `order_items` e `payments`;
- registro inicial de administrador para acesso futuro ao CMS;
- arquivo PHP de conexão com o banco em `server/connection.php`;
- dados iniciais de teste para usuários, produtos, pedidos, itens de pedido e pagamentos.

Por fim, a Aula Prática 3 adiciona:

- subprojeto `admin/` para o dashboard administrativo;
- tela de login com autenticação pela tabela `admins`;
- controle de sessão para proteger as páginas do dashboard;
- listagem paginada de pedidos com 5 itens por página;
- edição do status e exclusão de pedidos.

A Aula Prática 4 complementa o CMS com:

- logout administrativo com encerramento da sessão;
- listagem paginada de produtos com ações de editar dados, editar imagens e excluir;
- formulário para cadastro de novos produtos com upload da imagem principal para `assets/imgs`;
- página de edição dos dados do produto;
- página de edição das quatro imagens do produto;
- listagem paginada das contas de clientes cadastrados.

A Aula Prática 5 adiciona a vitrine pública da loja com:

- página `products.php` para listagem dos produtos cadastrados no banco;
- paginação pública com 8 itens por página;
- página `single_product.php` para exibição dos detalhes do produto;
- galeria com troca da imagem em evidência ao clicar nas miniaturas;
- integracao da navegacao publica entre inicio, catalogo e detalhe do produto.

A Aula Prática 6 adiciona a área do cliente com:

- página `register.php` para cadastro de novos usuários com validação e verificação de e-mail duplicado;
- página `login.php` para autenticação do cliente e abertura da sessão pública;
- página `account.php` para exibição de nome, e-mail e pedidos do usuário autenticado;
- formulário de troca de senha dentro da conta do cliente;
- logout do usuário pela própria página da conta;
- integração do ícone de usuário do header com o fluxo de login e conta.

A Aula Prática 7 complementa a loja com:

- página `cart.php` para visualização dos produtos adicionados ao carrinho;
- pagina `checkout.php` com formulario de UF, cidade e endereco;
- arquivo `server/place_order.php` para salvar pedidos e itens do pedido;
- lógica de adição, edição e remoção de itens no carrinho usando sessão;
- exibicao da quantidade total de itens ao lado do icone do carrinho no header;
- validacao de login na finalizacao da compra e criacao do pedido no banco com transacao.

---

## Stack e Dependências

Para reproduzir o projeto localmente, você precisa de:

- PHP 7.4+ ou superior;
- MySQL ou MariaDB;
- XAMPP com Apache e MySQL;
- navegador web atualizado;
- conexão com a internet para carregar dependências via CDN.

Bibliotecas e recursos utilizados:

- Bootstrap `5.1.1` via CDN;
- Font Awesome `5.10.0` via CDN;
- Google Fonts (`Inter` e `Playfair Display`);
- CSS customizado em `assets/css/style.css`.

---

## Estrutura do Projeto

```text
.
├── assets/
│   ├── css/
│   │   └── style.css
│   └── imgs/
├── admin/
│   ├── account.php
│   ├── add_product.php
│   ├── create_product.php
│   ├── delete_product.php
│   ├── edit_images.php
│   ├── edit_order.php
│   ├── edit_product.php
│   ├── header.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── products.php
│   └── sidemenu.php
├── layouts/
│   ├── footer.php
│   └── header.php
├── account.php
├── cart.php
├── checkout.php
├── login.php
├── products.php
├── register.php
├── server/
│   ├── connection.php
│   └── place_order.php
├── single_product.php
├── project_db.sql
└── index.php
```

---

## Como Rodar Localmente

Este projeto pode ser executado com XAMPP. O BrowserSync segue como opcional para facilitar o desenvolvimento visual.

### Pré-requisitos

- XAMPP com Apache e MySQL em execução;
- Node.js e `npx` instalados;
- projeto copiado para a pasta `htdocs` do XAMPP.

### 1. Servir o projeto com XAMPP

Coloque a pasta do projeto dentro de `htdocs`. Exemplo de acesso base:

```text
http://localhost/Project/index.php
```

### 2. Criar o banco de dados

Abra o `phpMyAdmin` no XAMPP e importe o arquivo `project_db.sql`. Isso criará:

- o banco `project_db`;
- as tabelas principais do e-commerce;
- o registro inicial da tabela `admins`;
- produtos e pedidos de exemplo para validar o dashboard admin.

### 3. Validar a conexão PHP

O arquivo `server/connection.php` já está configurado para conectar em:

- host: `localhost`
- usuário: `root`
- senha: vazia
- banco: `project_db`

### 4. Acessar o dashboard admin

Depois de importar o banco, você pode acessar o dashboard em:

```text
http://localhost/Project/admin/login.php
```

Credenciais iniciais cadastradas no banco:

- usuário: `admin` ou `admin@shop.com.br`
- senha: `123456`

### 5. Recursos do CMS administrativo

Depois de efetuar login no admin, o menu lateral permite acessar:

- `Pedidos`: pedidos com paginacao, edicao de status e exclusao;
- `Produtos`: produtos com paginacao, edicao, edicao de imagens e exclusao;
- `Contas`: listagem paginada dos usuarios cadastrados;
- `Adicionar produto`: formulario de criacao de produtos com upload da imagem principal;
- `Sair`: encerramento da sessao administrativa.

### 6. Iniciar o BrowserSync

Na raiz do projeto, execute:

```bash
npx browser-sync start --proxy "http://localhost" --files "index.php,layouts/*.php,assets/css/*.css,assets/imgs/*" --port 3000 --no-open
```

Depois, acesse no navegador:

```text
http://localhost:3000/Project/index.php

```
O BrowserSync fará o proxy do Apache e atualizará a página automaticamente sempre que houver mudanças nos arquivos monitorados.

---

## Observações

- Como Bootstrap, Font Awesome e Google Fonts são carregados por CDN, a página precisa de internet para exibir todos os estilos e ícones corretamente.
- As imagens de destaque do rodapé também são carregadas externamente.
- O `browser-sync` pode ser instalado automaticamente via `npx` no primeiro uso.
- As Aulas Práticas 2 e 3 dependem do banco `project_db` estar importado e do Apache/MySQL estarem em execução no XAMPP.

---

## Autor

Rafael Oliveira Lopes
