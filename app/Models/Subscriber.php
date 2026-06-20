<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'email',
    ];

    protected $casts = [
        'email' => 'string',
    ];

    /**
     * Scope for active subscribers
     */

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();
    }
}
