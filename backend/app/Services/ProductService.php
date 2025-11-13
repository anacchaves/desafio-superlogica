<?php

namespace App\Services;

use App\Exceptions\InvalidPriceChangeException;
use App\Exceptions\ProductCannotBeDeletedException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private Product $product
    ) {}

    public function createProduct(array $data): Product
    {
        return $this->product->create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        if (isset($data['price']) && $data['price'] != $product->price) {
            if (! $product->isPriceChangeValid($data['price'])) {
                $minPrice = $product->price * 0.7;
                $maxPrice = $product->price * 1.3;
                throw new InvalidPriceChangeException($minPrice, $maxPrice);
            }
        }

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product): void
    {
        if (! $product->canBeDeleted()) {
            throw new ProductCannotBeDeletedException;
        }

        $product->delete();
    }

    public function listProducts(array $filters = []): LengthAwarePaginator
    {
        $query = $this->product->newQuery();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }
}
