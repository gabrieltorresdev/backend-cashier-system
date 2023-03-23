<?php

namespace App\DTO;

use App\DTO\BaseDTO;

class TransactionDTO extends BaseDTO
{
    public ?string $id;
    public ?string $cashRegisterId;
    public ?float $value;
    public ?string $note;
    public ?string $type;
    public ?bool $finished;
    /** @var \App\DTO\ProductDTO[] */
    public ?array $products;
}
