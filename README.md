# Sistema de Gerenciamento de Produtos

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white)

Sistema Full-Stack de gerenciamento de produtos com autenticação completa, desenvolvido como parte do desafio técnico da Superlógica.
Um exemplo pode ser acessado em: [desafio.anachaves.dev.br](https://desafio.anachaves.dev.br/)

## 🐳 Como Executar com Docker

### Pré-requisitos

-   Docker 
-   Docker Compose 

### Início Rápido

1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd desafio-superlogica
```

2. Configure as variáveis de ambiente

```bash
cp .env.example .env
```

3. Inicie os containers

```bash
docker compose up -d
```

O sistema executará automaticamente:

-   Instalação de dependências
-   Migrations do banco de dados
-   Seeders com dados de teste

4. Acesse a aplicação

-   **Frontend:** http://localhost:3000
-   **Backend API:** http://localhost:8000

### Credenciais de Teste

```
Email: test@example.com
Senha: password123
```
## 🚀 Tecnologias

### Backend

-   **Laravel 12** - Framework PHP
-   **PHP 8.2+**
-   **PostgreSQL 16** - Banco de dados
-   **Laravel Sanctum** - Autenticação via tokens

### Frontend

-   **Vue 3** - Framework JavaScript
-   **TypeScript**
-   **Vite** - Build tool
-   **TailwindCSS** - Framework CSS
-   **Vue Router** - Roteamento SPA

### Infraestrutura

-   **Docker** - Containerização
-   **Docker Compose** - Orquestração
-   **Nginx** - Servidor web

## 📚 Documentação

Para mais informações, consulte a documentação completa:

-   [Funcionalidades](docs/FEATURES.md) - Lista completa de funcionalidades
-   [Regras de Negócio](docs/BUSINESS_RULES.md) - Regras implementadas no sistema
-   [API](docs/API.md) - Documentação completa da API REST
-   [Testes](docs/TESTING.md) - Como executar e criar testes
-   [Arquitetura](docs/ARCHITECTURE.md) - Estrutura e padrões do projeto
-   [Desenvolvimento](docs/DEVELOPMENT.md) - Guia para desenvolvimento local
