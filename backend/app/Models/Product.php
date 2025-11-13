<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->is_active = $product->stock > 0;
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('stock')) {
                $product->is_active = $product->stock > 0;
            }
        });
    }

    public function canBeDeleted(): bool
    {
        return $this->stock === 0;
    }

    public function isPriceChangeValid(float $newPrice): bool
    {
        $minPrice = $this->price * 0.7;
        $maxPrice = $this->price * 1.3;

        return $newPrice >= $minPrice && $newPrice <= $maxPrice;
    }

    public function getAllowedPriceRange(): array
    {
        return [
            'min' => round($this->price * 0.7, 2),
            'max' => round($this->price * 1.3, 2),
        ];
    }
}
