<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'phone_type_id',
        'phone',
        'order',
        'status',
    ];

    protected $casts = [
        'phone_type_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the phone type that owns this phone
     */
    public function phoneType()
    {
        return $this->belongsTo(PhoneType::class);
    }

    /**
     * Scope for active phones
     */

    /**
     * Scope for ordered phones
     */

    /**
     * Auto-increment order when creating
     */
    
} 