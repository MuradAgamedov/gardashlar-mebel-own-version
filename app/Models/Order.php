<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'products_id', // 🔹 əlavə olundu
        'full_name',
        'phone',
        'email',
        'city',
        'order_id',
        'text',
        'delivery_method',
        'payment_method',
        'selected_card',
        'month',
        'monthly_price',
        'status',
        'payment_status',
    ];

    /**
     * İstifadəçi ilə əlaqə (Order belongs to User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Məhsulla əlaqə (Order belongs to Product)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
