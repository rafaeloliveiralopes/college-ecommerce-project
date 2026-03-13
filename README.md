# Desenvolvimento de E-commerce com CMS

Projeto desenvolvido a partir do roteiro de aula prática da disciplina de Desenvolvimento de E-commerce com CMS. 

A proposta foi montar a estrutura inicial de uma vitrine de e-commerce com `header`, `footer`, menu responsivo e um banner principal, usando PHP apenas para reutilização de layout.

---

## Sobre o Projeto

Esta aplicação representa a base visual de uma homepage de e-commerce. O projeto foi organizado com includes em PHP para separar o topo e o rodapé da página, além de uma folha de estilos própria para personalizar a identidade visual da interface.

Atualmente, a página inicial inclui:

- navbar responsiva com logo, links de navegação e ícones de carrinho e usuário;
- seção principal com banner e chamada promocional;
- rodapé com navegação, contato, destaques e formas de pagamento;
- estilização customizada sobre componentes do Bootstrap.

---

## Stack e Dependências

Para reproduzir o projeto localmente, você precisa de:

- PHP 7.4+ ou superior;
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
└── index.php
```

---

## Como Rodar Localmente

Este projeto está sendo executado com XAMPP + BrowserSync.

### Pré-requisitos

- XAMPP com Apache em execução;
- Node.js e `npx` instalados;
- projeto copiado para a pasta `htdocs` do XAMPP.

### 1. Servir o projeto com XAMPP

Coloque a pasta do projeto dentro de `htdocs`. Exemplo de acesso base:

```text
http://localhost/Project/index.php
```

### 2. Iniciar o BrowserSync

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

- O projeto não utiliza banco de dados.
- Como Bootstrap, Font Awesome e Google Fonts são carregados por CDN, a página precisa de internet para exibir todos os estilos e ícones corretamente.
- As imagens de destaque do rodapé também são carregadas externamente.
- O `browser-sync` pode ser instalado automaticamente via `npx` no primeiro uso.

---

## Autor

Rafael Oliveira Lopes
