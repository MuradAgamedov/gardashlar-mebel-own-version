<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomePageProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="HomePageProjectCategories", description="Home page project categories API endpoints")
 */
class HomePageProjectCategoryController extends Controller
{
    use LocaleFromHeader;

    /**
     * Get home page project categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:home-page-project-categories:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $homePageProjectCategories = HomePageProjectCategory::with('projectCategory.translates')
                    ->active()
                    ->ordered()
                    ->get();

                return $homePageProjectCategories->map(function ($item) use ($languageId) {
                    // Get translation for the specified language
                    $translation = $item->projectCategory->translates()
                        ->where('language_id', $languageId)
                        ->first();
                    
                    // If no translation found, get the first available translation
                    if (!$translation) {
                        $translation = $item->projectCategory->translates()->first();
                    }

                    return [
                        'id' => $item->projectCategory->id,
                        'title' => $translation ? $translation->title : null,
                        'image_url' => $item->projectCategory->image_url
                    ];
                });
            });

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Ana səhifə layihə kateqoriyaları əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
                'data' => []
            ], 500);
        }
    }
}
