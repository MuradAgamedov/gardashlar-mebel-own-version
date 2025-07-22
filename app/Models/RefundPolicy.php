<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundPolicy extends Model
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
        return $this->hasMany(RefundPolicyTranslate::class);
    }
}
