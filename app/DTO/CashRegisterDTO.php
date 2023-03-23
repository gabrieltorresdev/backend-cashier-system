<?php

namespace App\DTO;

use App\DTO\BaseDTO;

class CashRegisterDTO extends BaseDTO
{
    public ?string $id;
    public ?string $userId;
    public ?float $initial_balance;
    public ?float $current_balance;
    public ?\DateTime $closedAt;
    /** @var \App\DTO\TransactionDTO[] */
    public ?array $transactions;
}
