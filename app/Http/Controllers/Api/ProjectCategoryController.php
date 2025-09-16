<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="ProjectCategories", description="Project categories API endpoints")
 */
class ProjectCategoryController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/project-categories",
     *     operationId="getAllProjectCategories",
     *     tags={"ProjectCategories"},
     *     summary="Get all project categories",
     *     description="Returns all active project categories with title and slug",
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         description="Language code (az, en, ru)",
     *         required=false,
     *         @OA\Schema(type="string", example="az")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object", 
     *                 @OA\Property(property="id", type="integer", example=1), 
     *                 @OA\Property(property="title", type="string", example="Soft Furniture Designs"), 
     *                 @OA\Property(property="slug", type="string", example="soft-furniture-designs"),
     *                 @OA\Property(property="seo_title", type="string", example="Soft Furniture Designs - Gardashlar"),
     *                 @OA\Property(property="seo_keywords", type="string", example="soft furniture, design, gardashlar"),
     *                 @OA\Property(property="seo_description", type="string", example="High quality soft furniture designs by Gardashlar"),
     *                 @OA\Property(property="image_alt_text", type="string", example="Soft furniture design category image"),
     *                 @OA\Property(property="image_url", type="string", example="https://example.com/category-image.jpg")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error occurred while getting project categories"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:project-categories:all:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $projectCategories = ProjectCategory::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }
                ])
                ->active()
                ->ordered()
                ->get();

                return $projectCategories->map(function ($category) use ($languageId) {
                    // Get translation for the specified language
                    $translation = $category->translates()
                        ->where('language_id', $languageId)
                        ->first();
                    
                    // If no translation found, get the first available translation
                    if (!$translation) {
                        $translation = $category->translates()->first();
                    }

        return [
            'id' => $category->id,
            'title' => $translation ? $translation->title : null,
            'slug' => $translation ? $translation->slug : null,
            'seo_title' => $translation ? $translation->seo_title : null,
            'seo_keywords' => $translation ? $translation->seo_keywords : null,
            'seo_description' => $translation ? $translation->seo_description : null,
            'image_alt_text' => $translation ? $translation->image_alt_text : null,
            'image_url' => $category->image_url
        ];
                })->filter(function ($item) {
                    // Filter out items without title or slug
                    return !empty($item['title']) && !empty($item['slug']);
                })->values();
            });

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Layihə kateqoriyaları əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
                'data' => []
            ], 500);
        }
    }
}
