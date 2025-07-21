<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAInvestor extends Model
{
    use HasFactory;

    protected $table = 'become_a_investors';
    protected $fillable = []; // No direct fields on this table

    /**
     * Get all translations for this investor
     */
    public function translates()
    {
        return $this->hasMany(BecomeAInvestorTranslate::class);
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
     * Get or create the first investor record
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
