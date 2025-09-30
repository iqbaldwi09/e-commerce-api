<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        return response()->json([
            'status' => 'success',
            'data'   => $products
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        return response()->json([
            'status' => 'success',
            'data'   => $product
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'data'    => $product
        ], 201);
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully',
            'data'    => $product
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
