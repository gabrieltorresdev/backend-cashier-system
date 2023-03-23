<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    /** @var Product */
    protected $model;

    public function decrementQuantityInStock(
        string $productId,
        float|int $quantityToDecrement
    ): int | false {
        $this->model = $this->model->find($productId);

        if (($this->model->stock->quantity - $quantityToDecrement) < 0) return false;

        return $this->model->stock->decrement('quantity', $quantityToDecrement);
    }
}
