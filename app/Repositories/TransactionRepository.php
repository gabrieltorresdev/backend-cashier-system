<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository extends BaseRepository
{
    /** @var Transaction */
    protected $model;

    public function isFinished(): ?bool
    {
        return $this->model->finished;
    }

    public function getCurrentProductQuantity(string $productId)
    {
        return $this->model->products()
            ->wherePivot('product_id', $productId)
            ->sum('quantity');
    }

    public function syncProducts(array $products): array
    {
        return $this->model->products()->syncWithoutDetaching($products);
    }

    public function hasProducts(): bool
    {
        return $this->model->products()->exists();
    }
}
