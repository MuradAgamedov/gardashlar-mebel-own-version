<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Language;

trait LocaleFromHeader
{
    /**
     * Get language ID from header locale string (expects lang_code like "en", "az", "tr")
     */
    protected function getLanguageIdFromHeader(Request $request): ?int
    {
        $locale = $request->header('locale') ;
       
        // Əgər locale string varsa və status aktivdirsə
        if (!empty($locale)) {
            $language = Language::where('lang_code', $locale)
                ->where('status', true)
                ->first();

            if ($language) {
                return $language->id;
            }
        }

        // Əgər düzgün locale deyilsə, default dilin ID-sini qaytar
        $default = Language::where('is_main_language', true)
            ->where('status', true)
            ->first();
      
        return $default?->id;
    }
}
