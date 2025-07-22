<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function translates()
    {
        return $this->hasMany(DeliveryPaymentTranslate::class);
    }
}
