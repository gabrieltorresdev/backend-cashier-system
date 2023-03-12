<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockType()
    {
        return $this->belongsTo(StockType::class);
    }
}
