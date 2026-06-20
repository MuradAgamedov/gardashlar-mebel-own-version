<?php

namespace App\Traits;

use App\Models\Language;

trait Translatable
{
    /**
     * Get translations for a specific language, or fallback to the main language.
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }
}
