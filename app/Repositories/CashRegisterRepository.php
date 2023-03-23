<?php

namespace App\Repositories;

use App\Models\CashRegister;

class CashRegisterRepository extends BaseRepository
{
    /** @var CashRegister */
    protected $model;

    public function getByUser(
        string $userId,
        bool $opened = true,
        string|array $relations = null
    ): ?array {
        $query = $this->model->query()
            ->where('user_id', '=', $userId)
            ->where('closed_at', $opened ? '=' : '<>', null);

        if (!is_null($relations))
            $query->with($relations);

        return $query->first()?->toArray();
    }

    public function handleOpen(string $userId): bool
    {
        $user = $this->model
            ->user()
            ->getRelated()
            ->find($userId);

        if (!$user) return false;

        $openedCashRegister = $user->cashRegisters()
            ->whereNull('closed_at')
            ->first();

        if (!empty($openedCashRegister)) return false;

        $lastClosedCashRegister = $this->model
            ->whereNotNull('closed_at')
            ->orderBy('closed_at', 'ASC')
            ->first();

        $created = $user->cashRegisters()
            ->create([
                'initial_balance' => $lastClosedCashRegister->current_balance ?? 0,
                'current_balance' => $lastClosedCashRegister->current_balance ?? 0
            ]);

        return !empty($created);
    }

    public function handleClose()
    {
        return is_null($this->model->closed_at)
            && $this->model->update(['closed_at' => now()]);
    }
}
