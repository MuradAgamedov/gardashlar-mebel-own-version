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
     *     path="/projects",
     *     operationId="getAllProjects",
     *     tags={"Projects"},
     *     summary="Get all projects",
     *     description="Returns paginated active projects with title, slug, company name and main image",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=9, default=9)
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
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=9),
     *                 @OA\Property(property="total", type="integer", example=18),
     *                 @OA\Property(property="last_page", type="integer", example=2),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=9)
     *             )
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
    public function getAllProjects(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 9);
            $cacheKey = "api:projects:all:lang:{$languageId}:page:{$page}:per_page:{$perPage}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId, $page, $perPage) {
                // Get all projects with pagination
                $projectsQuery = Project::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'category.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('is_main_image', true)->where('status', true);
                    }
                ])
                ->active()
                ->ordered();

                // Apply pagination
                $projects = $projectsQuery->paginate($perPage, ['*'], 'page', $page);

                // Transform the data
                $transformedData = $projects->getCollection()->map(function ($project) use ($languageId) {
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
                            // Get category translation
                $categoryTranslation = $project->category->translates()
                ->where('language_id', $languageId)
                ->first();
            
            if (!$categoryTranslation) {
                $categoryTranslation = $project->category->translates()->first();
            }

                    return [
                        'id' => $project->id,
                        'title' => $translation ? $translation->title : null,
                        'slug' => $translation ? $translation->slug : null,
                        'company_name' => $translation ? $translation->company_name : null,
                        'category_slug' => $categoryTranslation ? $categoryTranslation->slug : null,
                        'main_image' => $mainImage ? $mainImage->image_url : asset('images/default_image.webp')
                    ];
                })->filter(function ($item) {
                    // Filter out items without title or slug
                    return !empty($item['title']) && !empty($item['slug']);
                })->values();

                // Create pagination info
                $pagination = [
                    'current_page' => $projects->currentPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                    'last_page' => $projects->lastPage(),
                    'from' => $projects->firstItem(),
                    'to' => $projects->lastItem()
                ];

                return [
                    'data' => $transformedData,
                    'pagination' => $pagination
                ];
            });

            return response()->json([
                'success' => true, 
                'data' => $data['data'], 
                'pagination' => $data['pagination']
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Layihələr əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
                'data' => [],
                'pagination' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/projects/{slug}",
     *     operationId="getProjectsByCategorySlug",
     *     tags={"Projects"},
     *     summary="Get projects by category slug",
     *     description="Returns paginated active projects for a specific category with title, slug, company name and main image",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Project category slug",
     *         required=true,
     *         @OA\Schema(type="string", example="yumaq-mebellr")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=9, default=9)
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
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=9),
     *                 @OA\Property(property="total", type="integer", example=18),
     *                 @OA\Property(property="last_page", type="integer", example=2),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=9)
     *             )
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
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 9);
            $cacheKey = "api:projects:category:{$slug}:lang:{$languageId}:page:{$page}:per_page:{$perPage}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($slug, $languageId, $page, $perPage) {
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

                // Get projects for this category with pagination
                $projectsQuery = Project::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('is_main_image', true)->where('status', true);
                    }
                ])
                ->where('project_category_id', $category->id)
                ->active()
                ->ordered();

                // Apply pagination
                $projects = $projectsQuery->paginate($perPage, ['*'], 'page', $page);

                // Transform the data
                $transformedData = $projects->getCollection()->map(function ($project) use ($languageId) {
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
                        // Get category translation
                        $categoryTranslation = $project->category->translates()
                        ->where('language_id', $languageId)
                        ->first();
                        
                        if (!$categoryTranslation) {
                            $categoryTranslation = $project->category->translates()->first();
                        }

                    return [
                        'id' => $project->id,
                        'title' => $translation ? $translation->title : null,
                        'slug' => $translation ? $translation->slug : null,
                        'company_name' => $translation ? $translation->company_name : null,
                        'category_slug' => $categoryTranslation ? $categoryTranslation->slug : null,
                        'main_image' => $mainImage ? $mainImage->image_url : asset('images/default_image.webp')
                    ];
                })->filter(function ($item) {
                    // Filter out items without title or slug
                    return !empty($item['title']) && !empty($item['slug']);
                })->values();

                // Create pagination info
                $pagination = [
                    'current_page' => $projects->currentPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                    'last_page' => $projects->lastPage(),
                    'from' => $projects->firstItem(),
                    'to' => $projects->lastItem()
                ];

                return [
                    'data' => $transformedData,
                    'pagination' => $pagination
                ];
            });

            if ($data === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kateqoriya tapılmadı',
                    'data' => [],
                    'pagination' => null
                ], 404);
            }

            return response()->json([
                'success' => true, 
                'data' => $data['data'], 
                'pagination' => $data['pagination']
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Layihələr əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
                'data' => [],
                'pagination' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/project/{slug}",
     *     operationId="getProjectBySlug",
     *     tags={"Projects"},
     *     summary="Get project details by slug",
     *     description="Returns detailed information about a specific project including images, videos, and translations",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Project slug",
     *         required=true,
     *         @OA\Schema(type="string", example="modern-soft-furniture-design")
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
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Modern Soft Furniture Design"),
     *                 @OA\Property(property="slug", type="string", example="modern-soft-furniture-design"),
     *                 @OA\Property(property="company_name", type="string", example="Gardashlar Furniture"),
     *                 @OA\Property(property="description", type="string", example="Modern soft furniture design description"),
     *                 @OA\Property(property="category_slug", type="string", example="yumshaq-mebeller"),
     *                 @OA\Property(property="images", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="image_url", type="string", example="https://example.com/storage/projects/image.jpg"),
     *                     @OA\Property(property="is_main_image", type="boolean", example=true),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )),
     *                 @OA\Property(property="videos", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="video_link", type="string", example="https://youtube.com/watch?v=example"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Project not found"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error occurred while getting project"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getBySlug(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:project:details:{$slug}:lang:{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
                // Find the project by slug
                $project = Project::with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'category.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('status', true)->orderBy('order');
                    },
                    'videos' => function($query) {
                        $query->where('status', true)->orderBy('order');
                    }
                ])
                ->whereHas('translates', function($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->active()
                ->first();

                if (!$project) {
                    return null;
                }

                // Get translation for the specified language
                $translation = $project->translates()
                    ->where('language_id', $languageId)
                    ->first();
                
                // If no translation found, get the first available translation
                if (!$translation) {
                    $translation = $project->translates()->first();
                }

                // Get category translation
                $categoryTranslation = $project->category->translates()
                    ->where('language_id', $languageId)
                    ->first();
                
                if (!$categoryTranslation) {
                    $categoryTranslation = $project->category->translates()->first();
                }

                return [
                    'id' => $project->id,
                    'title' => $translation ? $translation->title : null,
                    'slug' => $translation ? $translation->slug : null,
                    'company_name' => $translation ? $translation->company_name : null,
                    'description' => $translation ? $translation->description : null,
                    'category_slug' => $categoryTranslation ? $categoryTranslation->slug : null,
                    'images' => $project->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'is_main_image' => $image->is_main_image,
                            'order' => $image->order
                        ];
                    }),
                    'videos' => $project->videos->map(function ($video) {
                        return [
                            'id' => $video->id,
                            'video_link' => $video->video_link,
                            'order' => $video->order
                        ];
                    })
                ];
            });

            if ($data === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layihə tapılmadı',
                    'data' => null
                ], 404);
            }

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Layihə əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
                'data' => null
            ], 500);
        }
    }

}
