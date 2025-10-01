<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compare extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    // User ilə əlaqə
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Product ilə əlaqə
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
