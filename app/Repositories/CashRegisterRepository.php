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

}
