# Documentação da API

## Endpoints

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

## Exemplos de Requisições

### Registro

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

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Listar Produtos

```bash
curl -X GET "http://localhost:8000/api/products?page=1&per_page=15" \
  -H "Authorization: Bearer {seu-token}"
```

### Criar Produto

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

### Atualizar Produto

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

### Excluir Produto

```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {seu-token}"
```

## Parâmetros de Busca e Filtro

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

## Credenciais de Teste

O sistema cria automaticamente um usuário de teste ao iniciar:

```
Email: test@example.com
Senha: password123
```

Use estas credenciais para fazer login e testar todas as funcionalidades do sistema.
