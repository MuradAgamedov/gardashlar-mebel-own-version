<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneType extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this phone type
     */
    public function translates()
    {
        return $this->hasMany(PhoneTypeTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get all phones for this type
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Scope for active phone types
     */

    /**
     * Scope for ordered phone types
     */

    /**
     * Auto-increment order when creating
     */
    
} 