<?php

namespace App\Http\Actions\Dashboard;

use App\DTO\CashRegisterDTO;
use App\DTO\TransactionDTO;
use App\Repositories\CashRegisterRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;

class UpdateTransactionAction
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private CashRegisterRepository $cashRegisterRepository,
        private ProductRepository $productRepository,
        private AddProductsToTransactionAction $addProductsToTransactionAction,
        private UpdateCashRegisterAction $updateCashRegisterAction,
        private CashRegisterDTO $cashRegisterDTO
    ) {
    }

    public function execute(TransactionDTO $dto): bool
    {
        $this->cashRegisterRepository->whereModel($dto->cashRegisterId);

        if (!$dto->id)
            $dto->id = $this->cashRegisterRepository->createTransaction($dto->toArray('products'));

        $this->transactionRepository->whereModel($dto->id);

        $transaction = $this->transactionRepository->get();

        if (!empty($transaction)) $dto->value = $transaction['value'];
        
        if (!empty($dto->products))
            $this->addProductsToTransactionAction->execute($dto);

        return $this->transactionRepository->update($dto->toArray());
    }
}
