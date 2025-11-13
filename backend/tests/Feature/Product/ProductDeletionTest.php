<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_with_zero_stock_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Produto excluído com sucesso',
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_with_positive_stock_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_deletion_error_message_is_clear(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Não é possível excluir um produto com estoque maior que zero',
            ]);
    }

    public function test_deleting_nonexistent_product_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/products/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Produto não encontrado',
            ]);
    }
}
