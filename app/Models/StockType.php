<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'decimals'
    ];

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
}
