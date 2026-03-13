# Desenvolvimento de E-commerce com CMS

Projeto desenvolvido a partir do roteiro de aula prática da disciplina de Desenvolvimento de E-commerce com CMS.

Até o momento, o repositório contempla a interface inicial da loja virtual, a estrutura de banco de dados do sistema e a continuidade do dashboard administrativo, seguindo as Aulas Práticas 1, 2, 3 e 4 do roteiro.

---

## Sobre o Projeto

Esta aplicação representa a base inicial de um e-commerce com CMS. O projeto foi organizado com includes em PHP para separar o topo e o rodapé da página, uma folha de estilos própria para personalizar a interface e uma camada inicial de persistência com MySQL.

Atualmente, a página inicial inclui:

- navbar responsiva com logo, links de navegação e ícones de carrinho e usuário;
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
├── server/
│   └── connection.php
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

- `Orders`: pedidos com paginação, edição de status e exclusão;
- `Products`: produtos com paginação, edição, edição de imagens e exclusão;
- `Account`: listagem paginada dos usuários cadastrados;
- `Add New Product`: formulário de criação de produtos com upload da imagem principal;
- `Logout`: encerramento da sessão administrativa.

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
- As funcionalidades da Aula Prática 4 também dependem do MySQL do XAMPP ativo para validar login, CRUD de produtos e listagem de usuários.
- O lint automático dos arquivos PHP do admin foi executado com `/opt/lampp/bin/php -l` e não encontrou erros de sintaxe.

---

## Autor

Rafael Oliveira Lopes
