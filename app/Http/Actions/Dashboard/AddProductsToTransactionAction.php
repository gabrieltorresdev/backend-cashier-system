<?php

namespace App\Http\Actions\Dashboard;

use App\DTO\TransactionDTO;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Validation\ValidationException;

class AddProductsToTransactionAction
{
    public function __construct(
        private ProductRepository $productRepository,
        private TransactionRepository $transactionRepository
    ) {
    }

    public function execute(TransactionDTO $dto): void
    {
        if ($dto->type !== 'sale' && $dto->type !== 'return')
            return;

        $products = $this->productRepository->whereIn($dto->pluck('products.id'));

        if (!$products) return;

        $products = merge_arrays($dto->toArray(null, 'products'), $products);

        $this->transactionRepository->whereModel($dto->id);

        $productsSync = [];

        foreach ($products as $key => $product) {
            if (!$this->productRepository->decrementQuantityInStock(
                $product['id'],
                $product['quantity']
            )) throw ValidationException::withMessages([
                "products.$key.quantity" => __("custom.product.quantity-bigger-than-stock")
            ]);

            $dto->value = bcadd($dto->value, $this->getTotalProductCost($product['cost'], $product['quantity']), 2);

            $productsSync[$product['id']] = [
                'quantity' => $this->sumTransactionProductQuantity(
                    $product['id'],
                    $product['quantity']
                )
            ];
        }

        $this->transactionRepository->syncProducts($productsSync);
    }

    private function getTotalProductCost(
        float $cost,
        float $quantity
    ) {
        return bcmul($cost, $quantity, 4);
    }

    private function sumTransactionProductQuantity(string $productId, float $productQuantity): float
    {
        $currentProductQuantity = $this->transactionRepository->getCurrentProductQuantity($productId);

        return bcadd($currentProductQuantity, $productQuantity, 4);
    }
}
