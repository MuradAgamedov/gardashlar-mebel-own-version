<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bg_image',
    ];

    public function translates()
    {
        return $this->hasMany(HomePageDiscountTranslate::class);
    }
}
