<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class LanguageController extends Controller
{
    use LocaleFromHeader;

    /**
     * Get all active languages ordered by order
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Set locale from header
            $this->setLocaleFromHeader($request);
            
            $languages = Cache::rememberForever('api_languages', function () {
                return Language::where('status', true)
                    ->orderBy('order')
                    ->select(['id', 'title', 'lang_code', 'lang_name', 'is_default', 'order'])
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $languages
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving languages: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
