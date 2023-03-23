<?php

namespace App\Http\Actions\Dashboard;

use App\DTO\CashRegisterDTO;
use App\Repositories\CashRegisterRepository;

class UpdateCashRegisterAction
{
    public function __construct(
        private CashRegisterRepository $cashRegisterRepository
    ) {
    }

    public function execute(CashRegisterDTO $dto): bool
    {
        $this->cashRegisterRepository->whereModel($dto->id);

        $cashRegister = $this->cashRegisterRepository->get();
        $dto->fill($cashRegister);

        $dto->current_balance = $this->cashRegisterRepository->getTotalTransactionsValues();

        if ($dto->current_balance < 0) {
            throw_exception(__("custom.cash-register.insufficient-balance"));
        }

        return $this->cashRegisterRepository->update($dto->toArray());
    }
}
