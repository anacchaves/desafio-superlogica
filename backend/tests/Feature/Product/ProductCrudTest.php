<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    // List Tests

    public function test_authenticated_user_can_list_products(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Create some products
        Product::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'products' => [
                        '*' => ['id', 'name', 'description', 'price', 'stock', 'is_active', 'created_at', 'updated_at'],
                    ],
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

    public function test_unauthenticated_user_cannot_list_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    }

    // Create Tests

    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'description', 'price', 'stock', 'is_active', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Test Product',
                    'description' => 'Test Description',
                    'price' => '99.99',
                    'stock' => 10,
                    'is_active' => true,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
        ]);
    }

    public function test_product_creation_requires_name(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $productData = [
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_product_creation_requires_positive_price(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 0,
            'stock' => 10,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_product_creation_requires_non_negative_stock(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => -1,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['stock']);
    }

    // Read Tests

    public function test_authenticated_user_can_view_product(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/products/'.$product->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'description', 'price', 'stock', 'is_active', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => 'Test Product',
                    'price' => '99.99',
                    'stock' => 10,
                ],
            ]);
    }

    public function test_viewing_nonexistent_product_returns_404(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/products/999999');

        $response->assertStatus(404);
    }

    // Update Tests

    public function test_authenticated_user_can_update_product(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 100.00,
            'stock' => 10,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'price' => 110.00, // Within 30% range
            'stock' => 15,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/products/'.$product->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'description', 'price', 'stock', 'is_active', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => 'Updated Name',
                    'description' => 'Updated Description',
                    'price' => '110.00',
                    'stock' => 15,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_updating_nonexistent_product_returns_404(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Name',
            'price' => 110.00,
            'stock' => 15,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/products/999999', $updateData);

        $response->assertStatus(404);
    }
}
