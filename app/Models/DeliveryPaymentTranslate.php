<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPaymentTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_payment_id',
        'language_id',
        'title',
        'content',
    ];

    public function deliveryPayment()
    {
        return $this->belongsTo(DeliveryPayment::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
