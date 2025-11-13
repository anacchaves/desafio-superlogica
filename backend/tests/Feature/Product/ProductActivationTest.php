<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductActivationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_product_with_zero_stock_is_created_as_inactive(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/products', [
                'name' => 'Produto Sem Estoque',
                'description' => 'Produto que será criado inativo',
                'price' => 100.00,
                'stock' => 0,
            ]);

        $response->assertStatus(201);

        $product = Product::latest()->first();
        $this->assertFalse($product->is_active);
    }

    public function test_product_with_positive_stock_is_created_as_active(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/products', [
                'name' => 'Produto Com Estoque',
                'description' => 'Produto que será criado ativo',
                'price' => 100.00,
                'stock' => 10,
            ]);

        $response->assertStatus(201);

        $product = Product::latest()->first();
        $this->assertTrue($product->is_active);
    }

    public function test_product_becomes_inactive_when_stock_updated_to_zero(): void
    {
        $product = Product::factory()->create([
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'stock' => 0,
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    public function test_product_becomes_active_when_stock_updated_to_positive(): void
    {
        $product = Product::factory()->create([
            'stock' => 0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'stock' => 5,
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertTrue($product->is_active);
    }

    public function test_product_remains_active_when_stock_stays_positive(): void
    {
        $product = Product::factory()->create([
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'stock' => 15,
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertTrue($product->is_active);
    }

    public function test_product_remains_inactive_when_stock_stays_zero(): void
    {
        $product = Product::factory()->create([
            'stock' => 0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'name' => 'Nome Atualizado',
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertFalse($product->is_active);
    }
}
