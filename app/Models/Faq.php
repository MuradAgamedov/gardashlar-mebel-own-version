<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function translates()
    {
        return $this->hasMany(FaqTranslate::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
