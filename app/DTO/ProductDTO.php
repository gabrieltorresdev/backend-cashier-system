<?php

namespace App\DTO;

use App\DTO\BaseDTO;

class ProductDTO extends BaseDTO
{
    public ?string $id = null;
    public ?string $code = null;
    public ?string $name = null;
    public ?float $cost = null;
    public ?float $quantity = null;
    public ?string $description = null;
}
