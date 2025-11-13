<?php

namespace Tests\Unit\Services;

use App\Exceptions\InvalidPriceChangeException;
use App\Exceptions\ProductCannotBeDeletedException;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user for authentication
        $this->user = User::factory()->create();

        // Instantiate the service
        $this->productService = new ProductService(new Product);
    }

    /**
     * Test that createProduct creates a product successfully
     */
    public function test_create_product_creates_product_successfully(): void
    {
        // Arrange
        $this->actingAs($this->user);

        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.00,
            'stock' => 10,
        ];

        // Act
        $product = $this->productService->createProduct($data);

        // Assert
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(100.00, $product->price);
        $this->assertEquals(10, $product->stock);
        $this->assertTrue($product->is_active); // Should be active with stock > 0
    }

    /**
     * Test that updateProduct throws exception for invalid price change
     */
    public function test_update_product_throws_exception_for_invalid_price_change(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act & Assert - Try to increase price by 40% (exceeds 30% limit)
        $this->expectException(InvalidPriceChangeException::class);
        $this->expectExceptionMessage('O preço não pode variar mais de 30% do valor atual (R$ 70.00 - R$ 130.00)');

        $this->productService->updateProduct($product, ['price' => 140.00]);
    }

    /**
     * Test that updateProduct throws exception for excessive price decrease
     */
    public function test_update_product_throws_exception_for_excessive_price_decrease(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act & Assert - Try to decrease price by 40% (exceeds 30% limit)
        $this->expectException(InvalidPriceChangeException::class);
        $this->expectExceptionMessage('O preço não pode variar mais de 30% do valor atual (R$ 70.00 - R$ 130.00)');

        $this->productService->updateProduct($product, ['price' => 60.00]);
    }

    /**
     * Test that updateProduct allows valid price change
     */
    public function test_update_product_allows_valid_price_change(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - Increase price by 20% (within 30% limit)
        $updatedProduct = $this->productService->updateProduct($product, ['price' => 120.00]);

        // Assert
        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals(120.00, $updatedProduct->price);
    }

    /**
     * Test that updateProduct allows valid price decrease
     */
    public function test_update_product_allows_valid_price_decrease(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - Decrease price by 20% (within 30% limit)
        $updatedProduct = $this->productService->updateProduct($product, ['price' => 80.00]);

        // Assert
        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals(80.00, $updatedProduct->price);
    }

    /**
     * Test that updateProduct allows updates without price change
     */
    public function test_update_product_allows_updates_without_price_change(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - Update only name, not price
        $updatedProduct = $this->productService->updateProduct($product, ['name' => 'Updated Name']);

        // Assert
        $this->assertEquals('Updated Name', $updatedProduct->name);
        $this->assertEquals(100.00, $updatedProduct->price); // Price unchanged
    }

    /**
     * Test that deleteProduct throws exception when stock is positive
     */
    public function test_delete_product_throws_exception_when_stock_is_positive(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'stock' => 10,
        ]);

        // Act & Assert
        $this->expectException(ProductCannotBeDeletedException::class);
        $this->expectExceptionMessage('Não é possível excluir um produto com estoque maior que zero');

        $this->productService->deleteProduct($product);
    }

    /**
     * Test that deleteProduct succeeds when stock is zero
     */
    public function test_delete_product_succeeds_when_stock_is_zero(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'stock' => 0,
        ]);

        $productId = $product->id;

        // Act
        $this->productService->deleteProduct($product);

        // Assert
        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    /**
     * Test that listProducts applies search filter
     */
    public function test_list_products_applies_search_filter(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Notebook Dell',
            'description' => 'High performance laptop',
        ]);

        Product::factory()->create([
            'name' => 'Mouse Logitech',
            'description' => 'Wireless mouse',
        ]);

        Product::factory()->create([
            'name' => 'Keyboard',
            'description' => 'Mechanical keyboard',
        ]);

        // Act - Search for "Notebook" (case-sensitive match)
        $results = $this->productService->listProducts(['search' => 'Notebook']);

        // Assert
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Notebook Dell', $results->first()->name);
    }

    /**
     * Test that listProducts applies search filter on description
     */
    public function test_list_products_applies_search_filter_on_description(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Product A',
            'description' => 'Contains wireless technology',
        ]);

        Product::factory()->create([
            'name' => 'Product B',
            'description' => 'Wired connection',
        ]);

        // Act - Search for "wireless"
        $results = $this->productService->listProducts(['search' => 'wireless']);

        // Assert
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Product A', $results->first()->name);
    }

    /**
     * Test that listProducts applies active filter
     */
    public function test_list_products_applies_active_filter(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Active Product',
            'stock' => 10,
        ]);

        Product::factory()->create([
            'name' => 'Inactive Product',
            'stock' => 0,
        ]);

        // Act - Filter for active products only
        $results = $this->productService->listProducts(['is_active' => true]);

        // Assert
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Active Product', $results->first()->name);
        $this->assertTrue($results->first()->is_active);
    }

    /**
     * Test that listProducts applies inactive filter
     */
    public function test_list_products_applies_inactive_filter(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Active Product',
            'stock' => 10,
        ]);

        Product::factory()->create([
            'name' => 'Inactive Product',
            'stock' => 0,
        ]);

        // Act - Filter for inactive products only
        $results = $this->productService->listProducts(['is_active' => false]);

        // Assert
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Inactive Product', $results->first()->name);
        $this->assertFalse($results->first()->is_active);
    }

    /**
     * Test that listProducts combines search and filter
     */
    public function test_list_products_combines_search_and_filter(): void
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Active Mouse',
            'stock' => 10,
        ]);

        Product::factory()->create([
            'name' => 'Inactive Mouse',
            'stock' => 0,
        ]);

        Product::factory()->create([
            'name' => 'Active Keyboard',
            'stock' => 5,
        ]);

        // Act - Search for "Mouse" (case-sensitive match) and filter for active only
        $results = $this->productService->listProducts([
            'search' => 'Mouse',
            'is_active' => true,
        ]);

        // Assert
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Active Mouse', $results->first()->name);
        $this->assertTrue($results->first()->is_active);
    }

    /**
     * Test that listProducts returns paginated results
     */
    public function test_list_products_returns_paginated_results(): void
    {
        // Arrange - Create 20 products
        Product::factory()->count(20)->create();

        // Act - Request with custom per_page
        $results = $this->productService->listProducts(['per_page' => 10]);

        // Assert
        $this->assertEquals(10, $results->count());
        $this->assertEquals(20, $results->total());
        $this->assertEquals(2, $results->lastPage());
    }

    /**
     * Test that listProducts uses default pagination
     */
    public function test_list_products_uses_default_pagination(): void
    {
        // Arrange - Create 20 products
        Product::factory()->count(20)->create();

        // Act - Request without per_page (should default to 15)
        $results = $this->productService->listProducts();

        // Assert
        $this->assertEquals(15, $results->count());
        $this->assertEquals(20, $results->total());
    }
}
