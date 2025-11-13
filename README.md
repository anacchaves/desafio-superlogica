# Sistema de Gerenciamento de Produtos

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white)

## 📋 Sobre o Projeto

Sistema Full-Stack de gerenciamento de produtos com autenticação completa, desenvolvido como parte do desafio técnico da Superlógica.

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP moderno e elegante
- **PHP 8.2+** - Linguagem de programação
- **PostgreSQL 16** - Banco de dados relacional
- **Laravel Sanctum** - Autenticação via tokens API

### Frontend
- **Vue 3** - Framework JavaScript progressivo
- **TypeScript** - Superset tipado do JavaScript
- **Vite** - Build tool e dev server ultrarrápido
- **TailwindCSS** - Framework CSS utility-first
- **Vue Router** - Roteamento para SPA

### Infraestrutura
- **Docker** - Containerização da aplicação
- **Docker Compose** - Orquestração de containers
- **Nginx** - Servidor web de alta performance

## ⚙️ Funcionalidades

### Autenticação
- ✅ Registro de novos usuários
- ✅ Login com email e senha
- ✅ Logout com revogação de token
- ✅ Proteção de rotas autenticadas

### Gerenciamento de Produtos
- ✅ Listagem de produtos com paginação
- ✅ Criação de novos produtos
- ✅ Visualização de detalhes do produto
- ✅ Edição de produtos existentes
- ✅ Exclusão de produtos (com restrições)
- ✅ Busca por nome e descrição
- ✅ Filtros por status (ativo/inativo)

### Interface do Usuário
- ✅ Feedback visual com notificações toast
- ✅ Indicadores de carregamento
- ✅ Confirmações para ações destrutivas
- ✅ Validação de formulários em tempo real

## 🧠 Regras de Negócio

O sistema implementa três regras de negócio principais:

### 1. Ativação Automática Baseada em Estoque
**Regra:** Produtos com estoque zero são automaticamente marcados como inativos.

- Quando um produto é criado com `stock = 0`, o campo `is_active` é automaticamente definido como `false`
- Quando um produto é criado com `stock > 0`, o campo `is_active` é automaticamente definido como `true`
- Ao atualizar o estoque para zero, o produto é automaticamente desativado
- Ao atualizar o estoque para um valor positivo, o produto é automaticamente ativado

**Exemplo:**
```json
// Criar produto sem estoque
POST /api/products
{
  "name": "Produto Teste",
  "price": 100.00,
  "stock": 0
}
// Resultado: is_active = false (automático)
```

### 2. Validação de Variação de Preço (±30%)
**Regra:** O preço de um produto não pode variar mais de 30% (para cima ou para baixo) do valor atual.

- Ao atualizar o preço, o sistema calcula: `preço_mínimo = preço_atual × 0.7`
- E também: `preço_máximo = preço_atual × 1.3`
- O novo preço deve estar dentro desta faixa
- Se a validação falhar, o sistema retorna erro 422 com a faixa permitida

**Exemplo:**
```json
// Produto atual: price = 100.00
// Faixa permitida: R$ 70.00 - R$ 130.00

PUT /api/products/1
{
  "price": 150.00  // ❌ Erro: excede 30%
}

PUT /api/products/1
{
  "price": 120.00  // ✅ Sucesso: dentro da faixa
}
```

### 3. Restrição de Exclusão por Estoque
**Regra:** Produtos só podem ser excluídos se o estoque for zero.

- Produtos com `stock > 0` não podem ser deletados
- Tentativa de exclusão retorna erro 400 com mensagem explicativa
- Apenas produtos com `stock = 0` podem ser removidos do sistema

**Exemplo:**
```json
// Produto com stock = 5
DELETE /api/products/1
// Resultado: 400 Bad Request
// "Não é possível excluir um produto com estoque maior que zero"

// Produto com stock = 0
DELETE /api/products/2
// Resultado: 200 OK - Produto excluído
```

## 🐳 Como Executar com Docker

### Pré-requisitos
- Docker instalado (versão 20.10 ou superior)
- Docker Compose instalado (versão 2.0 ou superior)

### Passo a Passo

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
cd <nome-do-projeto>
```

2. **Configure as variáveis de ambiente**

O arquivo `.env` já está configurado na raiz do projeto. Caso necessário, você pode ajustar as configurações:

```bash
# Banco de Dados
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=product_management
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Backend
APP_URL=http://localhost:8000

# Frontend
VITE_API_URL=http://localhost:8000
```

3. **Inicie os containers**
```bash
docker compose up
```

Aguarde alguns instantes enquanto os containers são construídos e iniciados. O sistema executará automaticamente:
- Instalação de dependências
- Migrations do banco de dados
- Seeders com dados de teste

4. **Acesse a aplicação**

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000

### Comandos Úteis

```bash
# Parar os containers
docker compose down

# Parar e remover volumes (limpa o banco de dados)
docker compose down -v

# Ver logs dos containers
docker compose logs -f

# Ver logs de um serviço específico
docker compose logs -f backend
docker compose logs -f frontend

# Reconstruir os containers
docker compose up --build

# Executar comandos no container do backend
docker compose exec backend sh
```

## 🔑 Credenciais de Teste

O sistema cria automaticamente um usuário de teste ao iniciar:

```
Email: test@example.com
Senha: password123
```

Use estas credenciais para fazer login e testar todas as funcionalidades do sistema.

### Dados de Exemplo

O seeder também cria produtos de exemplo que demonstram todas as regras de negócio:
- Produtos ativos (com estoque)
- Produtos inativos (sem estoque)
- Produtos com diferentes faixas de preço
- Produtos que podem e não podem ser excluídos

## 🧪 Como Executar os Testes

O projeto possui uma suite completa de testes automatizados que validam todas as funcionalidades.

### Executar Todos os Testes

```bash
# Entre no container do backend
docker compose exec backend sh
[compose.yaml]
indent_size = 4
# Execute os testes
php artisan test
```

### Executar Testes Específicos

```bash
# Testes de autenticação
php artisan test --filter AuthenticationTest

# Testes de produtos
php artisan test --filter Product

# Testes com cobertura
php artisan test --coverage
```

### Suites de Teste

O projeto inclui:

**Feature Tests:**
- ✅ Autenticação (registro, login, logout)
- ✅ CRUD de produtos
- ✅ Ativação automática baseada em estoque
- ✅ Validação de variação de preço
- ✅ Restrição de exclusão
- ✅ Busca e filtros

**Unit Tests:**
- ✅ Métodos do modelo Product
- ✅ Lógica de negócio do ProductService

## 📡 Endpoints da API

### Autenticação

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| POST | `/api/register` | Registrar novo usuário | Não |
| POST | `/api/login` | Fazer login | Não |
| POST | `/api/logout` | Fazer logout | Sim |
| GET | `/api/user` | Obter usuário autenticado | Sim |

### Produtos

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| GET | `/api/products` | Listar produtos (paginado) | Sim |
| POST | `/api/products` | Criar novo produto | Sim |
| GET | `/api/products/{id}` | Obter detalhes do produto | Sim |
| PUT | `/api/products/{id}` | Atualizar produto | Sim |
| DELETE | `/api/products/{id}` | Excluir produto | Sim |

### Exemplos de Requisições

**Registro:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123",
    "password_confirmation": "senha123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Listar Produtos:**
```bash
curl -X GET "http://localhost:8000/api/products?page=1&per_page=15" \
  -H "Authorization: Bearer {seu-token}"
```

**Criar Produto:**
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer {seu-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Notebook Dell",
    "description": "Notebook Dell Inspiron 15",
    "price": 3500.00,
    "stock": 10
  }'
```

**Atualizar Produto:**
```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {seu-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Notebook Dell Atualizado",
    "price": 3800.00,
    "stock": 5
  }'
```

**Excluir Produto:**
```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {seu-token}"
```

### Parâmetros de Busca e Filtro

```bash
# Buscar por nome ou descrição
GET /api/products?search=notebook

# Filtrar por status ativo
GET /api/products?is_active=1

# Filtrar por status inativo
GET /api/products?is_active=0

# Combinar busca e filtro
GET /api/products?search=mouse&is_active=1

# Paginação customizada
GET /api/products?page=2&per_page=20
```

## 📁 Estrutura do Projeto

```
.
├── backend/                    # Aplicação Laravel (API)
│   ├── app/
│   │   ├── Exceptions/        # Exceções customizadas
│   │   ├── Http/
│   │   │   ├── Controllers/   # Controllers da API
│   │   │   ├── Requests/      # Form Requests (validação)
│   │   │   └── Resources/     # API Resources (transformação)
│   │   ├── Models/            # Models Eloquent
│   │   └── Services/          # Lógica de negócio
│   ├── database/
│   │   ├── migrations/        # Migrations do banco
│   │   └── seeders/           # Seeders de dados
│   ├── routes/
│   │   └── api.php           # Rotas da API
│   └── tests/                # Testes automatizados
│       ├── Feature/          # Testes de integração
│       └── Unit/             # Testes unitários
│
├── frontend/                  # Aplicação Vue.js (SPA)
│   ├── src/
│   │   ├── components/       # Componentes reutilizáveis
│   │   ├── composables/      # Composables Vue
│   │   ├── router/           # Configuração de rotas
│   │   ├── views/            # Páginas da aplicação
│   │   └── types/            # Tipos TypeScript
│   └── public/               # Assets estáticos
│
├── docker-compose.yml        # Orquestração dos containers
├── .env                      # Variáveis de ambiente
└── README.md                 # Este arquivo
```

### Arquitetura Backend

O backend segue uma arquitetura em camadas:

- **Controllers:** Recebem requisições e retornam respostas
- **Form Requests:** Validam dados de entrada
- **Services:** Contêm lógica de negócio
- **Models:** Representam entidades do banco de dados
- **Resources:** Transformam dados para resposta da API
- **Exceptions:** Tratam erros de negócio

### Arquitetura Frontend

O frontend utiliza composables para lógica reutilizável:

- **Views:** Páginas da aplicação
- **Components:** Componentes UI reutilizáveis
- **Composables:** Lógica compartilhada (auth, API, notificações)
- **Router:** Gerenciamento de rotas e guards

## 🛠️ Desenvolvimento Local (Sem Docker)

Se preferir executar sem Docker:

### Backend

```bash
cd backend

# Instalar dependências
composer install

# Configurar .env
cp .env.example .env
php artisan key:generate

# Executar migrations e seeders
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

### Frontend

```bash
cd frontend

# Instalar dependências
npm install

# Iniciar dev server
npm run dev
```

## 🔧 Troubleshooting

### Problema: Containers não iniciam

**Solução:**
```bash
# Limpar containers e volumes
docker compose down -v

# Reconstruir
docker compose up --build
```

### Problema: Erro de permissão no backend

**Solução:**
```bash
# Ajustar permissões
docker compose exec backend chmod -R 777 storage bootstrap/cache
```

### Problema: Frontend não conecta ao backend

**Verificar:**
- Backend está rodando em http://localhost:8000
- Variável `VITE_API_URL` está correta no `.env`
- CORS está configurado no backend

### Problema: Migrations não executam

**Solução:**
```bash
# Executar manualmente
docker compose exec backend php artisan migrate --seed
```

### Problema: Testes falham

**Verificar:**
- Banco de dados de teste está configurado
- Executar dentro do container: `docker compose exec backend php artisan test`

## 📝 Notas de Desenvolvimento

### Boas Práticas Implementadas

- ✅ Separação de responsabilidades (Controllers, Services, Models)
- ✅ Validação em Form Requests dedicados
- ✅ Transformação de dados com API Resources
- ✅ Exceções customizadas para regras de negócio
- ✅ Testes automatizados com alta cobertura
- ✅ Código limpo e bem documentado
- ✅ Commits semânticos e organizados

### Padrões de Código

- **Backend:** PSR-12
- **Frontend:** ESLint + Prettier
- **Commits:** Conventional Commits

### Segurança

- ✅ Senhas hasheadas com bcrypt
- ✅ Autenticação via tokens (Laravel Sanctum)
- ✅ Validação e sanitização de inputs
- ✅ CORS configurado adequadamente
- ✅ Rate limiting em endpoints sensíveis
- ✅ Variáveis sensíveis em .env

## 📄 Licença

Este projeto foi desenvolvido como parte de um desafio técnico.

## 👤 Autor

Desenvolvido para o desafio técnico da Superlógica.

---

**Dica:** Para uma melhor experiência, use o sistema através do frontend em http://localhost:3000 após executar `docker compose up`.
