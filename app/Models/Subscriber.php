<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
    ];

    protected $casts = [
        'email' => 'string',
    ];

    /**
     * Scope for active subscribers
     */
    public function scopeActive($query)
    {
        return $query;
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();
    }
}
