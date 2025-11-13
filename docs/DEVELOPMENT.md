# Desenvolvimento Local

## Desenvolvimento Sem Docker

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

## Comandos Úteis

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

## Troubleshooting

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
