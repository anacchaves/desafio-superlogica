# Testes

O projeto possui uma suite completa de testes automatizados que validam todas as funcionalidades.

## Executar Todos os Testes

```bash
# Entre no container do backend
docker compose exec backend sh

# Execute os testes
php artisan test
```

## Executar Testes Específicos

```bash
# Testes de autenticação
php artisan test --filter AuthenticationTest

# Testes de produtos
php artisan test --filter Product

# Testes com cobertura
php artisan test --coverage
```

## Suites de Teste

O projeto inclui:

### Feature Tests

- ✅ Autenticação (registro, login, logout)
- ✅ CRUD de produtos
- ✅ Ativação automática baseada em estoque
- ✅ Validação de variação de preço
- ✅ Restrição de exclusão
- ✅ Busca e filtros

### Unit Tests

- ✅ Métodos do modelo Product
- ✅ Lógica de negócio do ProductService
