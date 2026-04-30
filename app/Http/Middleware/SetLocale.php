<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Try to get locale from cookie first
        $locale = Cookie::get('locale');
        
        // If no cookie, try from query parameter
        if (!$locale && $request->has('lang')) {
            $locale = $request->get('lang');
            // Set cookie for future requests
            Cookie::queue('locale', $locale, 525600); // 1 year in minutes
        }
        
        // If still no locale, use default or accept-language header
        if (!$locale) {
            $locale = config('app.locale', 'en');
        }
        
        // Validate locale (only allow az, en, ru)
        if (!in_array($locale, ['az', 'en', 'ru'])) {
            $locale = config('app.locale', 'en');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
}
