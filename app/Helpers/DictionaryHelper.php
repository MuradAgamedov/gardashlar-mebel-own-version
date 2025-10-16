<?php

namespace App\Helpers;

use App\Models\Dictionary;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;

class DictionaryHelper
{
    /**
     * Get translation by keyword and language code
     * 
     * @param string $keyword
     * @param string|null $langCode (az, en, ru, etc.)
     * @return string
     */
    public static function trans(string $keyword, ?string $langCode = null): string
    {
        // If no language code provided, use main language
        if (!$langCode) {
            $language = Language::getMainLanguage();
            $languageId = $language ? $language->id : 1;
        } else {
            // Get language by code
            $language = Cache::rememberForever("language_code_{$langCode}", function () use ($langCode) {
                return Language::where('lang_code', $langCode)->where('status', true)->first();
            });
            
            if (!$language) {
                return $keyword; // Return keyword if language not found
            }
            
            $languageId = $language->id;
        }

        // Cache key for translation
        $cacheKey = "dictionary_trans_{$keyword}_{$languageId}";

        return Cache::rememberForever($cacheKey, function () use ($keyword, $languageId) {
            $dictionary = Dictionary::where('keyword', $keyword)->first();
            
            if (!$dictionary) {
                return $keyword; // Return keyword if not found in dictionary
            }

            $translation = $dictionary->translates()
                ->where('language_id', $languageId)
                ->first();

            return $translation ? $translation->value : $keyword;
        });
    }

    /**
     * Get translation by keyword and language ID
     * 
     * @param string $keyword
     * @param int|null $languageId
     * @return string
     */
    public static function transById(string $keyword, ?int $languageId = null): string
    {
        if (!$languageId) {
            $language = Language::getMainLanguage();
            $languageId = $language ? $language->id : 1;
        }

        $cacheKey = "dictionary_trans_{$keyword}_{$languageId}";

        return Cache::rememberForever($cacheKey, function () use ($keyword, $languageId) {
            $dictionary = Dictionary::where('keyword', $keyword)->first();
            
            if (!$dictionary) {
                return $keyword;
            }

            $translation = $dictionary->translates()
                ->where('language_id', $languageId)
                ->first();

            return $translation ? $translation->value : $keyword;
        });
    }

    /**
     * Get all translations for a keyword
     * 
     * @param string $keyword
     * @return array
     */
    public static function getAllTranslations(string $keyword): array
    {
        $cacheKey = "dictionary_all_trans_{$keyword}";

        return Cache::rememberForever($cacheKey, function () use ($keyword) {
            $dictionary = Dictionary::where('keyword', $keyword)->first();
            
            if (!$dictionary) {
                return [];
            }

            return $dictionary->translates()
                ->with('language:id,lang_code,title')
                ->get()
                ->mapWithKeys(function ($translation) {
                    return [$translation->language->lang_code => $translation->value];
                })
                ->toArray();
        });
    }

    /**
     * Clear cache for specific keyword
     * 
     * @param string $keyword
     * @return void
     */
    public static function clearCache(string $keyword): void
    {
        $languages = Language::active()->get();
        
        foreach ($languages as $language) {
            Cache::forget("dictionary_trans_{$keyword}_{$language->id}");
        }
        
        Cache::forget("dictionary_all_trans_{$keyword}");
    }

    /**
     * Clear all dictionary cache
     * 
     * @return void
     */
    public static function clearAllCache(): void
    {
        $keywords = Dictionary::pluck('keyword');
        
        foreach ($keywords as $keyword) {
            self::clearCache($keyword);
        }
    }
}
