<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressType extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the translations for this address type
     */
    public function translates()
    {
        return $this->hasMany(AddressTypeTranslate::class);
    }

    /**
     * Get the addresses for this address type
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get translation for current language
     */
    public function getTranslation($languageId = null)
    {
        if (!$languageId) {
            $languageId = app()->getLocale() === 'az' ? 1 : 2; // Default to Azerbaijani
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Get title for current language
     */
    public function getTitle($languageId = null)
    {
        $translation = $this->getTranslation($languageId);
        return $translation ? $translation->title : '';
    }

    /**
     * Scope for active address types
     */

    /**
     * Scope for ordered address types
     */

    /**
     * Auto-increment order when creating
     */
    
} 