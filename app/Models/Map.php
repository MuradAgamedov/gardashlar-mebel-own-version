<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'map',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Scope for active maps
     */

    /**
     * Scope for ordered maps
     */

    /**
     * Auto-increment order when creating
     */
    
} 