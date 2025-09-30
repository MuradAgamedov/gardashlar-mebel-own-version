<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    // Mass assignment üçün sütunları əlavə edirik
    protected $fillable = [
        'user_id',
        'product_id',
        'color_id',
        'count',
    ];

    // Əlaqələr

    // Cart sahibi olan istifadəçi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cart-a əlavə olunmuş məhsul
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Əgər color_id varsa, məhsulun rəngi
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
