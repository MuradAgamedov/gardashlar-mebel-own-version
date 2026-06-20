<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dictionary extends Model
{
    use App\Traits\Translatable;
    protected $fillable = [
        'keyword'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the translations for this dictionary item
     */
    public function translates(): HasMany
    {
        return $this->hasMany(DictionaryTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get value by keyword and language
     */
    public static function getValue($keyword, $languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }

        $dictionary = static::where('keyword', $keyword)->first();
        if (!$dictionary) {
            return $keyword;
        }

        $translation = $dictionary->translates()->where('language_id', $languageId)->first();
        return $translation ? $translation->value : $keyword;
    }
}
