<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    /**
     * Get all active languages ordered by order
     */
    public function index(): JsonResponse
    {
        try {
            $languages = Cache::remember('api_languages', 3600, function () {
                return Language::where('status', true)
                    ->orderBy('order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Languages retrieved successfully',
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
