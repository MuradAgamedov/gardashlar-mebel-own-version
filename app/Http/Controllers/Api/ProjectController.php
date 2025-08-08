<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Projects", description="Projects API endpoints")
 */
class ProjectController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/projects/{slug}",
     *     operationId="getProjectsByCategorySlug",
     *     tags={"Projects"},
     *     summary="Get projects by category slug",
     *     description="Returns all active projects for a specific category with title, slug, company name and main image",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Project category slug",
     *         required=true,
     *         @OA\Schema(type="string", example="yumaq-mebellr")
     *     ),
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
     *                 @OA\Property(property="title", type="string", example="Modern Sofa Design"),
     *                 @OA\Property(property="slug", type="string", example="modern-sofa-design"),
     *                 @OA\Property(property="company_name", type="string", example="Gardashlar Furniture"),
     *                 @OA\Property(property="main_image", type="string", example="https://example.com/storage/projects/image.jpg")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error occurred while getting projects"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getByCategorySlug(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:projects:category:{$slug}:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
                // Find the project category by slug
                $category = ProjectCategory::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->whereHas('translates', function($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->active()
                ->first();

                if (!$category) {
                    return null;
                }

                // Get projects for this category
                $projects = Project::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('is_main_image', true)->where('status', true);
                    }
                ])
                ->where('project_category_id', $category->id)
                ->active()
                ->ordered()
                ->get();

                return $projects->map(function ($project) use ($languageId) {
                    // Get translation for the specified language
                    $translation = $project->translates()
                        ->where('language_id', $languageId)
                        ->first();
                    
                    // If no translation found, get the first available translation
                    if (!$translation) {
                        $translation = $project->translates()->first();
                    }

                    // Get main image
                    $mainImage = $project->images()->where('is_main_image', true)->where('status', true)->first();

                    return [
                        'id' => $project->id,
                        'title' => $translation ? $translation->title : null,
                        'slug' => $translation ? $translation->slug : null,
                        'company_name' => $translation ? $translation->company_name : null,
                        'main_image' => $mainImage ? $mainImage->image_url : null
                    ];
                })->filter(function ($item) {
                    // Filter out items without title or slug
                    return !empty($item['title']) && !empty($item['slug']);
                })->values();
            });

            if ($data === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kateqoriya tapılmadı',
                    'data' => []
                ], 404);
            }

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Layihələr əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
                'data' => []
            ], 500);
        }
    }
}
