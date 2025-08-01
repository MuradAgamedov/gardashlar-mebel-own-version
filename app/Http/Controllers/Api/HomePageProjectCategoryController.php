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
 * @OA\Get(
 *     path="/api/home-page-project-categories",
 *     operationId="getHomePageProjectCategories",
 *     tags={"Ana Səhifə Layihə Kateqoriyaları"},
 *     summary="Ana səhifədə görünəcək layihə kateqoriyalarını əldə et",
 *     description="Ana səhifədə göstəriləcək aktiv layihə kateqoriyalarının siyahısını qaytarır",
 *     @OA\Parameter(
 *         name="Accept-Language",
 *         in="header",
 *         description="Dil kodu (az, en, ru)",
 *         required=false,
 *         @OA\Schema(type="string", example="az")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Uğurlu cavab",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1, description="Layihə kateqoriyasının ID-si"),
 *                     @OA\Property(property="title", type="string", example="Tikinti Layihələri", description="Kateqoriyanın adı"),
 *                     @OA\Property(property="image_url", type="string", nullable=true, example="https://admin.gardaslar.coder.az/storage/project-categories/image.jpg", description="Şəklin tam URL-i")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server xətası",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Ana səhifə layihə kateqoriyaları əldə edilərkən xəta baş verdi"),
 *             @OA\Property(property="data", type="array", example=[])
 *         )
 *     )
 * )
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
