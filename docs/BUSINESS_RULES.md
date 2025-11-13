# Regras de Negócio

O sistema implementa três regras de negócio principais:

## 1. Ativação Automática Baseada em Estoque

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

## 2. Validação de Variação de Preço (±30%)

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

## 3. Restrição de Exclusão por Estoque

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
