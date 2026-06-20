<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'image',
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean'
    ];

    /**
     * Get all translations for this administration
     */
    public function translates()
    {
        return $this->hasMany(AdministrationTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active administrations
     */

    /**
     * Scope for ordered administrations
     */

    /**
     * Auto-increment order when creating
     */
    
} 