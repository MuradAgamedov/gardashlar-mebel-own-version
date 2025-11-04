<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Language;
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
     * Clear project cache for all languages
     */
    private function clearProjectCache($projectSlug = null)
    {
        $languages = Language::all();
        foreach ($languages as $language) {
            // Clear general project caches
            Cache::forget("api:projects:lang:{$language->id}");
            Cache::forget("api:projects:page:1:per_page:9:lang:{$language->id}");
            
            // Clear specific project cache if slug provided
            if ($projectSlug) {
                Cache::forget("api:project:details:{$projectSlug}:lang:{$language->id}");
            }
        }
    }


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

  
public function getByCategorySlug(Request $request, string $slug): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 9);
        $cacheKey = "api:projects:category:{$slug}:lang:{$languageId}:page:{$page}:per_page:{$perPage}";
        
        $data = Cache::rememberForever($cacheKey, function () use ($slug, $languageId, $page, $perPage) {
            // 🔹 Kateqoriyanı tapırıq
            $category = ProjectCategory::with(['translates.language'])
                ->whereHas('translates', function($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->active()
                ->first();

            if (!$category) {
                return null;
            }

            // 🔹 Kateqoriyanın bütün dillərdəki slug-ları
            $categorySlugTranslations = $category->translates->mapWithKeys(function ($t) {
                return [$t->language->lang_code => $t->slug];
            });

            // 🔹 Kateqoriyaya aid layihələri gətiririk
            $projectsQuery = Project::with([
                'translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'images' => function($query) {
                    $query->where('is_main_image', true)->where('status', true);
                },
                'category.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->where('project_category_id', $category->id)
            ->active()
            ->ordered();

            // 🔹 Pagination
            $projects = $projectsQuery->paginate($perPage, ['*'], 'page', $page);

            // 🔹 Layihə datalarının transformasiyası
            $transformedData = $projects->getCollection()->map(function ($project) use ($languageId) {
                $translation = $project->translates->first();
                $mainImage = $project->images->first();
                $categoryTranslation = $project->category?->translates?->first();

                return [
                    'id' => $project->id,
                    'title' => $translation?->title,
                    'slug' => $translation?->slug,
                    'company_name' => $translation?->company_name,
                    'category_slug' => $categoryTranslation?->slug,
                    'main_image' => $mainImage?->image_url ?? asset('images/default_image.webp')
                ];
            })->filter(fn($item) => !empty($item['title']) && !empty($item['slug']))->values();

            // 🔹 Oxşar layihələr
            $similarProjects = Project::with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'category.translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('is_main_image', true)->where('status', true)
            ])
            ->where('project_category_id', $category->id)
            ->active()
            ->ordered()
            ->limit(4)
            ->get()
            ->map(function ($similarProject) {
                $similarTranslation = $similarProject->translates->first();
                $similarCategoryTranslation = $similarProject->category->translates->first();
                $similarMainImage = $similarProject->images->first();
                
                return [
                    'id' => $similarProject->id,
                    'title' => $similarTranslation?->title,
                    'slug' => $similarTranslation?->slug,
                    'company_name' => $similarTranslation?->company_name,
                    'category_slug' => $similarCategoryTranslation?->slug,
                    'main_image' => $similarMainImage?->image_url ?? asset('images/default_image.webp')
                ];
            });

            // 🔹 Pagination məlumatları
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
                'pagination' => $pagination,
                'similar_projects' => $similarProjects,
                'translates' => [
                    'category_slug' => $categorySlugTranslations
                ]
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
            'pagination' => $data['pagination'],
            'similar_projects' => $data['similar_projects'],
            'translates' => $data['translates']
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


   
  public function getBySlug(Request $request, string $slug): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $cacheKey = "api:project:details:{$slug}:lang:{$languageId}";
        
        $data = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
            // 🔹 Layihəni tapırıq (bütün dilləri yükləyirik)
            $project = Project::with([
                'translates.language',
                'category.translates.language',
                'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                'videos' => fn($q) => $q->where('status', true)->orderBy('order')
            ])
            ->whereHas('translates', fn($q) => $q->where('slug', $slug))
            ->active()
            ->first();

            if (!$project) {
                return null;
            }

            // 🔹 Mövcud dildə tərcümə
            $translation = $project->translates->firstWhere('language_id', $languageId);
            $categoryTranslation = $project->category?->translates?->firstWhere('language_id', $languageId);

            // 🔹 Layihənin bütün dillərdə slug-ları
            $slugTranslations = $project->translates->mapWithKeys(function ($t) {
                return [$t->language->lang_code => $t->slug];
            });

            // 🔹 Kateqoriyanın bütün dillərdə slug-ları
            $categorySlugTranslations = $project->category?->translates?->mapWithKeys(function ($t) {
                return [$t->language->lang_code => $t->slug];
            }) ?? collect();

            // 🔹 Oxşar layihələr
            $similarProjects = Project::with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'category.translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('is_main_image', true)->where('status', true)
            ])
            ->where('project_category_id', $project->project_category_id)
            ->where('id', '!=', $project->id)
            ->active()
            ->ordered()
            ->limit(4)
            ->get()
            ->map(function ($similarProject) {
                $similarTranslate = $similarProject->translates->first();
                $similarCategoryTranslate = $similarProject->category?->translates?->first();
                $similarMainImage = $similarProject->images->first();

                return [
                    'id' => $similarProject->id,
                    'title' => $similarTranslate?->title,
                    'slug' => $similarTranslate?->slug,
                    'company_name' => $similarTranslate?->company_name,
                    'category_slug' => $similarCategoryTranslate?->slug,
                    'category_title' => $similarCategoryTranslate?->title,
                    'main_image' => $similarMainImage?->image_url ?? asset('images/default_image.webp')
                ];
            });

            // 🔹 Əsas məlumat
            return [
                'id' => $project->id,
                'title' => $translation?->title,
                'slug' => $translation?->slug,
                'company_name' => $translation?->company_name,
                'description' => $translation?->description,
                'seo_title' => $translation?->seo_title,
                'seo_keywords' => $translation?->seo_keywords,
                'seo_description' => $translation?->seo_description,
                'category_slug' => $categoryTranslation?->slug,
                'category_title' => $categoryTranslation?->title,
                'images' => $project->images->map(fn($image) => [
                    'id' => $image->id,
                    'image_url' => $image->image_url,
                    'is_main_image' => $image->is_main_image,
                ]),
                'videos' => $project->videos->map(fn($video) => [
                    'id' => $video->id,
                    'video_link' => $video->video_link,
                    'video_id' => explode("?", $video->video_link)[1] ?? null,
                ]),
                'similar_projects' => $similarProjects,
                'translates' => [
                    'slug' => $slugTranslations,
                    'category_slug' => $categorySlugTranslations
                ]
            ];
        });

        if ($data === null) {
            return response()->json([
                'success' => false,
                'message' => 'Layihə tapılmadı',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Layihə əldə edilərkən xəta baş verdi: ' . $e->getMessage(), 
            'data' => null
        ], 500);
    }
}


}
