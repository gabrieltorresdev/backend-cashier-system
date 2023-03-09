<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'total_amount'
    ];

    public function cashier()
    {
        return $this->belongsTo(Cashier::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_transaction');
    }
}
