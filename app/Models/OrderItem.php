<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    protected $casts = ['price' => 'decimal:2', 'total' => 'decimal:2'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
