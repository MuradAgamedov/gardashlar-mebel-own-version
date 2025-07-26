<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Language;

trait LocaleFromHeader
{
    /**
     * Get locale from header and set application locale
     */
    protected function setLocaleFromHeader(Request $request): void
    {
        $locale = $request->header('locale');
        
        if ($locale) {
            // Check if the locale exists in our languages table
            $language = Language::where('lang_code', $locale)
                ->where('status', true)
                ->first();
            
            if ($language) {
                app()->setLocale($locale);
            } else {
                // If locale not found, use default language
                $defaultLanguage = Language::where('is_default', true)
                    ->where('status', true)
                    ->first();
                
                if ($defaultLanguage) {
                    app()->setLocale($defaultLanguage->lang_code);
                }
            }
        } else {
            // If no locale header, use default language
            $defaultLanguage = Language::where('is_default', true)
                ->where('status', true)
                ->first();
            
            if ($defaultLanguage) {
                app()->setLocale($defaultLanguage->lang_code);
            }
        }
    }

    /**
     * Get current locale from header or default
     */
    protected function getLocaleFromHeader(Request $request): string
    {
        $locale = $request->header('locale');
        
        if ($locale) {
            $language = Language::where('lang_code', $locale)
                ->where('status', true)
                ->first();
            
            if ($language) {
                return $locale;
            }
        }
        
        // Return default language code
        $defaultLanguage = Language::where('is_default', true)
            ->where('status', true)
            ->first();
        
        return $defaultLanguage ? $defaultLanguage->lang_code : 'en';
    }
} 