<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidPriceChangeException;
use App\Exceptions\ProductCannotBeDeletedException;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->query('search'),
            'is_active' => $request->query('is_active'),
            'per_page' => $request->query('per_page', 15),
        ];

        $products = $this->productService->listProducts($filters);

        return response()->json(
            new ProductCollection($products),
            200
        );
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ], 200);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $product = $this->productService->createProduct($data);

        return response()->json([
            'success' => true,
            'message' => 'Produto criado com sucesso',
            'data' => new ProductResource($product),
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            $updatedProduct = $this->productService->updateProduct(
                $product,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Produto atualizado com sucesso',
                'data' => new ProductResource($updatedProduct),
            ], 200);
        } catch (InvalidPriceChangeException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => [
                    'price' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->deleteProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Produto excluído com sucesso',
            ], 200);
        } catch (ProductCannotBeDeletedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
