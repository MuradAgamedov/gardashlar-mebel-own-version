<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

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
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered phones
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($phone) {
            if (!isset($phone->order)) {
                $maxOrder = static::max('order');
                $phone->order = $maxOrder + 1;
            }
        });
    }
} 