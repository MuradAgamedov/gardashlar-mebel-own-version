<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProjectListResource;
use App\Http\Resources\Api\ProjectDetailResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class ProjectController extends Controller
{
    use LocaleFromHeader;

    private function clearProjectCache($projectSlug = null)
    {
        $languages = Language::all();
        foreach ($languages as $language) {
            Cache::forget("api:projects:lang:{$language->id}");
            Cache::forget("api:projects:page:1:per_page:9:lang:{$language->id}");
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

            $projects = Cache::rememberForever($cacheKey, function () use ($languageId, $page, $perPage) {
                return Project::with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'category.translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('is_main_image', true)->where('status', true),
                ])
                ->active()
                ->ordered()
                ->paginate($perPage, ['*'], 'page', $page);
            });

            $collection = $projects->getCollection()->filter(fn($p) => !empty($p->translates->first()?->title) && !empty($p->translates->first()?->slug))->values();

            return response()->json([
                'success' => true,
                'data' => ProjectListResource::collection($collection),
                'pagination' => [
                    'current_page' => $projects->currentPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                    'last_page' => $projects->lastPage(),
                    'from' => $projects->firstItem(),
                    'to' => $projects->lastItem(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layihələr əldə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => [],
                'pagination' => null,
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
                $category = ProjectCategory::with(['translates.language'])
                    ->whereHas('translates', fn($q) => $q->where('slug', $slug))
                    ->active()
                    ->first();

                if (!$category) {
                    return null;
                }

                $categorySlugTranslations = $category->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);

                $projects = Project::with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('is_main_image', true)->where('status', true),
                    'category.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->where('project_category_id', $category->id)
                ->active()
                ->ordered()
                ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'projects' => $projects,
                    'categorySlugTranslations' => $categorySlugTranslations,
                    'category_id' => $category->id,
                ];
            });

            if ($data === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kateqoriya tapılmadı',
                    'data' => [],
                    'pagination' => null,
                ], 404);
            }

            $projects = $data['projects'];
            $collection = $projects->getCollection()
                ->filter(fn($p) => !empty($p->translates->first()?->title) && !empty($p->translates->first()?->slug))
                ->values();

            $similarProjects = Project::with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'category.translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('is_main_image', true)->where('status', true),
            ])
            ->where('project_category_id', $data['category_id'])
            ->active()
            ->ordered()
            ->limit(4)
            ->get();

            return response()->json([
                'success' => true,
                'data' => ProjectListResource::collection($collection),
                'pagination' => [
                    'current_page' => $projects->currentPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                    'last_page' => $projects->lastPage(),
                    'from' => $projects->firstItem(),
                    'to' => $projects->lastItem(),
                ],
                'similar_projects' => ProjectListResource::collection($similarProjects),
                'translates' => ['category_slug' => $data['categorySlugTranslations']],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layihələr əldə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => [],
                'pagination' => null,
            ], 500);
        }
    }

    public function getBySlug(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:project:details:{$slug}:lang:{$languageId}";

            $project = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
                return Project::with([
                    'translates.language',
                    'category.translates.language',
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'videos' => fn($q) => $q->where('status', true)->orderBy('order'),
                ])
                ->whereHas('translates', fn($q) => $q->where('slug', $slug))
                ->active()
                ->first();
            });

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layihə tapılmadı',
                    'data' => null,
                ], 404);
            }

            $resource = new ProjectDetailResource($project);
            $resource->languageId = $languageId;

            return response()->json([
                'success' => true,
                'data' => $resource,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layihə əldə edilərkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
