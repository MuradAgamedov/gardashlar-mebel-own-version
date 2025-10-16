<?php

use App\Helpers\DictionaryHelper;

if (!function_exists('dict')) {
    /**
     * Get dictionary translation by keyword and language code
     * 
     * @param string $keyword
     * @param string|null $langCode
     * @return string
     */
    function dict(string $keyword, ?string $langCode = null): string
    {
        return DictionaryHelper::trans($keyword, $langCode);
    }
}

if (!function_exists('dict_id')) {
    /**
     * Get dictionary translation by keyword and language ID
     * 
     * @param string $keyword
     * @param int|null $languageId
     * @return string
     */
    function dict_id(string $keyword, ?int $languageId = null): string
    {
        return DictionaryHelper::transById($keyword, $languageId);
    }
}

if (!function_exists('dict_all')) {
    /**
     * Get all translations for a keyword
     * 
     * @param string $keyword
     * @return array
     */
    function dict_all(string $keyword): array
    {
        return DictionaryHelper::getAllTranslations($keyword);
    }
}
