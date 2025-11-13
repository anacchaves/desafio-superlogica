<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * Test that canBeDeleted returns true when stock is zero
     */
    public function test_can_be_deleted_returns_true_when_stock_is_zero(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 0,
        ]);

        // Act
        $result = $product->canBeDeleted();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test that canBeDeleted returns false when stock is positive
     */
    public function test_can_be_deleted_returns_false_when_stock_is_positive(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 5,
        ]);

        // Act
        $result = $product->canBeDeleted();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test that isPriceChangeValid returns true for valid increase
     */
    public function test_is_price_change_valid_returns_true_for_valid_increase(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - 20% increase (within 30% limit)
        $result = $product->isPriceChangeValid(120.00);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test that isPriceChangeValid returns true for valid decrease
     */
    public function test_is_price_change_valid_returns_true_for_valid_decrease(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - 20% decrease (within 30% limit)
        $result = $product->isPriceChangeValid(80.00);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test that isPriceChangeValid returns false for excessive increase
     */
    public function test_is_price_change_valid_returns_false_for_excessive_increase(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - 40% increase (exceeds 30% limit)
        $result = $product->isPriceChangeValid(140.00);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test that isPriceChangeValid returns false for excessive decrease
     */
    public function test_is_price_change_valid_returns_false_for_excessive_decrease(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - 40% decrease (exceeds 30% limit)
        $result = $product->isPriceChangeValid(60.00);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test that isPriceChangeValid returns true at exactly 30% increase boundary
     */
    public function test_is_price_change_valid_returns_true_at_exactly_30_percent_increase(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - exactly 30% increase
        $result = $product->isPriceChangeValid(130.00);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test that isPriceChangeValid returns true at exactly 30% decrease boundary
     */
    public function test_is_price_change_valid_returns_true_at_exactly_30_percent_decrease(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act - exactly 30% decrease
        $result = $product->isPriceChangeValid(70.00);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test that getAllowedPriceRange returns correct values
     */
    public function test_get_allowed_price_range_returns_correct_values(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
        ]);

        // Act
        $range = $product->getAllowedPriceRange();

        // Assert
        $this->assertIsArray($range);
        $this->assertArrayHasKey('min', $range);
        $this->assertArrayHasKey('max', $range);
        $this->assertEquals(70.00, $range['min']);
        $this->assertEquals(130.00, $range['max']);
    }

    /**
     * Test that getAllowedPriceRange works with different price values
     */
    public function test_get_allowed_price_range_with_different_prices(): void
    {
        // Arrange
        $product = new Product([
            'name' => 'Test Product',
            'price' => 250.00,
            'stock' => 10,
        ]);

        // Act
        $range = $product->getAllowedPriceRange();

        // Assert
        $this->assertEquals(175.00, $range['min']); // 250 * 0.7
        $this->assertEquals(325.00, $range['max']); // 250 * 1.3
    }
}
