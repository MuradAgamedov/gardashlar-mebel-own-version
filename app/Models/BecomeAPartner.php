<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAPartner extends Model
{
    use HasFactory;

    protected $table = 'become_a_partners';

    protected $fillable = [];

    /**
     * Get all translations for this become a partner
     */
    public function translates()
    {
        return $this->hasMany(BecomeAPartnerTranslate::class);
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Get first record or create if not exists
     */
    public static function getFirstOrCreate()
    {
        $record = static::first();
        
        if (!$record) {
            $record = static::create();
        }
        
        return $record;
    }
}
