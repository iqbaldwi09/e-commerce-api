<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Validation\ValidationException;

class ProductService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    public function getById(int $id)
    {
        $product = $this->productRepository->findById($id);

        if (! $product) {
            throw ValidationException::withMessages([
                'product' => ['Product not found.'],
            ]);
        }

        return $product;
    }

    public function create(array $data)
    {
        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->productRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
