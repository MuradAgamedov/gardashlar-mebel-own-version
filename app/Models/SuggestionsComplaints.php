<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionsComplaints extends Model
{
    use HasFactory;

    protected $table = 'suggestions_complaints';

    protected $fillable = ['id'];

    /**
     * Get all translations for this section
     */
    public function translates()
    {
        return $this->hasMany(SuggestionsComplaintsTranslate::class);
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
     * Get first or create a single record for this section
     */
    public static function getFirstOrCreate()
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
