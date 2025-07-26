<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Language;

trait LocaleFromHeader
{
    /**
     * Get language ID directly from header (expects numeric language_id)
     */
    protected function getLanguageIdFromHeader(Request $request): ?int
    {
        $languageId = (int) $request->header('locale');

        // Əgər rəqəmsə və status aktivdirsə
        if ($languageId > 0) {
            $language = Language::where('id', $languageId)
                ->where('status', true)
                ->first();

            if ($language) {
                return $language->id;
            }
        }

        // Əgər düzgün ID deyilsə, default dilin ID-sini qaytar
        $default = Language::where('is_default', true)
            ->where('status', true)
            ->first();

        return $default?->id;
    }
}
