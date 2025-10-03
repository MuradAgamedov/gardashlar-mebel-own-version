<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        'payment_status', // 🔹 əlavə olundu
    ];

    /**
     * İstifadəçi ilə əlaqə (Order belongs to User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
