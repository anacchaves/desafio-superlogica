<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_products_can_be_searched_by_name(): void
    {
        // Create products with different names
        Product::factory()->create([
            'name' => 'Notebook Dell',
            'description' => 'A laptop computer',
            'stock' => 10,
        ]);

        Product::factory()->create([
            'name' => 'Mouse Logitech',
            'description' => 'A computer mouse',
            'stock' => 20,
        ]);

        Product::factory()->create([
            'name' => 'Teclado Mecânico',
            'description' => 'A mechanical keyboard',
            'stock' => 15,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?search=Notebook');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.products');
        $response->assertJsonPath('data.products.0.name', 'Notebook Dell');
    }

    public function test_products_can_be_searched_by_description(): void
    {
        // Create products with different descriptions
        Product::factory()->create([
            'name' => 'Product A',
            'description' => 'This is a gaming mouse with RGB',
            'stock' => 10,
        ]);

        Product::factory()->create([
            'name' => 'Product B',
            'description' => 'This is a wireless keyboard',
            'stock' => 20,
        ]);

        Product::factory()->create([
            'name' => 'Product C',
            'description' => 'This is a gaming headset',
            'stock' => 15,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?search=gaming');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.products');
    }

    public function test_products_can_be_filtered_by_active_status(): void
    {
        // Create active products (stock > 0)
        Product::factory()->count(3)->create([
            'stock' => 10,
        ]);

        // Create inactive products (stock = 0)
        Product::factory()->count(2)->create([
            'stock' => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?is_active=1');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data.products');

        // Verify all returned products are active
        $products = $response->json('data.products');
        foreach ($products as $product) {
            $this->assertTrue($product['is_active']);
        }
    }

    public function test_products_can_be_filtered_by_inactive_status(): void
    {
        // Create active products (stock > 0)
        Product::factory()->count(3)->create([
            'stock' => 10,
        ]);

        // Create inactive products (stock = 0)
        Product::factory()->count(2)->create([
            'stock' => 0,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?is_active=0');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.products');

        // Verify all returned products are inactive
        $products = $response->json('data.products');
        foreach ($products as $product) {
            $this->assertFalse($product['is_active']);
        }
    }

    public function test_products_are_paginated(): void
    {
        // Create 20 products
        Product::factory()->count(20)->create([
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data.products');
        $response->assertJsonStructure([
            'data' => [
                'products',
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                    'from',
                    'to',
                ],
            ],
        ]);
    }

    public function test_pagination_metadata_is_correct(): void
    {
        // Create 25 products
        Product::factory()->count(25)->create([
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?per_page=10&page=2');

        $response->assertStatus(200);
        $response->assertJsonPath('data.pagination.current_page', 2);
        $response->assertJsonPath('data.pagination.per_page', 10);
        $response->assertJsonPath('data.pagination.total', 25);
        $response->assertJsonPath('data.pagination.last_page', 3);
        $response->assertJsonPath('data.pagination.from', 11);
        $response->assertJsonPath('data.pagination.to', 20);
    }

    public function test_search_and_filter_can_be_combined(): void
    {
        // Create active products with "Mouse" in name
        Product::factory()->count(2)->create([
            'name' => 'Mouse Gamer',
            'stock' => 10,
        ]);

        // Create inactive products with "Mouse" in name
        Product::factory()->count(2)->create([
            'name' => 'Mouse Wireless',
            'stock' => 0,
        ]);

        // Create active products without "Mouse" in name
        Product::factory()->count(3)->create([
            'name' => 'Teclado Mecânico',
            'stock' => 15,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?search=Mouse&is_active=1');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.products');

        // Verify all returned products match both criteria
        $products = $response->json('data.products');
        foreach ($products as $product) {
            $this->assertTrue($product['is_active']);
            $this->assertStringContainsString('Mouse', $product['name']);
        }
    }
}
