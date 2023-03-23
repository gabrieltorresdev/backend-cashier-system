<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'cost',
        'description'
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)->withPivot('quantity');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
