<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailType extends Model
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
     * Get all translations for this email type
     */
    public function translates()
    {
        return $this->hasMany(EmailTypeTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get all emails for this type
     */
    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Scope for active email types
     */

    /**
     * Scope for ordered email types
     */

    /**
     * Auto-increment order when creating
     */
    
} 