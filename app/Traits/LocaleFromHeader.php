<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Language;

trait LocaleFromHeader
{
    /**
     * Get language ID from header (based on locale string like 'az', 'en')
     */
    protected function getLanguageIdFromHeader(Request $request): ?int
    {
        $locale = $request->header('locale');

        // Yoxla: əgər locale varsa, uyğun language-i tap
        if ($locale) {
            $language = Language::where('lang_code', $locale)
                ->where('status', true)
                ->first();

            if ($language) {
                return $language->id;
            }
        }

        // Əgər tapılmadısa, default dili qaytar
        $default = Language::where('is_default', true)
            ->where('status', true)
            ->first();

        return $default?->id;
    }
}
