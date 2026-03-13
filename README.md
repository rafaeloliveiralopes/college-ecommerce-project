# Desenvolvimento de E-commerce com CMS

Projeto desenvolvido a partir do roteiro de aula prática da disciplina de Desenvolvimento de E-commerce com CMS.

Até o momento, o repositório contempla a interface inicial da loja virtual e a estrutura de banco de dados do sistema, seguindo as Aulas Práticas 1 e 2 do roteiro.

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
- arquivo PHP de conexão com o banco em `server/connection.php`.

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
- o registro inicial da tabela `admins`.

### 3. Validar a conexão PHP

O arquivo `server/connection.php` já está configurado para conectar em:

- host: `localhost`
- usuário: `root`
- senha: vazia
- banco: `project_db`

### 4. Iniciar o BrowserSync

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
- A Aula Prática 2 foi validada localmente com XAMPP, Apache e MySQL em execução.
- A conexão com o banco foi testada com sucesso em ambiente real.

---

## Autor

Rafael Oliveira Lopes
