<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_price_change_within_30_percent_is_allowed(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'price' => 120.00, // 20% increase
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals(120.00, $product->price);
    }

    public function test_price_increase_above_30_percent_is_rejected(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'price' => 140.00, // 40% increase
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);
    }

    public function test_price_decrease_below_30_percent_is_rejected(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'price' => 60.00, // 40% decrease
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);
    }

    public function test_price_at_exactly_30_percent_increase_is_allowed(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'price' => 130.00, // Exactly 30% increase
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals(130.00, $product->price);
    }

    public function test_price_at_exactly_30_percent_decrease_is_allowed(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'price' => 70.00, // Exactly 30% decrease
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals(70.00, $product->price);
    }

    public function test_error_message_includes_allowed_price_range(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'price' => 150.00, // Invalid price
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);

        $errorMessage = $response->json('errors.price.0');
        $this->assertStringContainsString('R$ 70.00', $errorMessage);
        $this->assertStringContainsString('R$ 130.00', $errorMessage);
    }

    public function test_price_validation_only_applies_when_price_is_being_updated(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'name' => 'Nome Atualizado',
                'description' => 'Nova descrição',
            ]);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals('Nome Atualizado', $product->name);
        $this->assertEquals(100.00, $product->price); // Price unchanged
    }
}
