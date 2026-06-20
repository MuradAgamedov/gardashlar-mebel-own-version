<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'address_type_id',
        'order',
        'status',
    ];

    protected $casts = [
        'address_type_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the address type that owns this address
     */
    public function addressType()
    {
        return $this->belongsTo(AddressType::class);
    }

    /**
     * Get the translations for this address
     */
    public function translates()
    {
        return $this->hasMany(AddressTranslate::class);
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
     * Get address for current language
     */
    public function getAddress($languageId = null)
    {
        $translation = $this->getTranslation($languageId);
        return $translation ? $translation->address : '';
    }

    /**
     * Scope for active addresses
     */

    /**
     * Scope for ordered addresses
     */

    /**
     * Auto-increment order when creating
     */
    
} 