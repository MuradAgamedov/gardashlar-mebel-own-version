<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IndividualProject;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Individual Projects",
 *     description="API Endpoints for Individual Projects"
 * )
 */
class IndividualProjectController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/individual-projects",
     *     summary="Get all active individual projects",
     *     description="Returns list of active individual projects with translated content based on header language",
     *     tags={"Individual Projects"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Individual projects retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Individual projects retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Ev Layihəsi"),
     *                     @OA\Property(property="description", type="string", example="Müasir ev layihəsi"),
     *                     @OA\Property(property="image_url", type="string", example="https://example.com/storage/projects/project1.jpg"),
     *                     @OA\Property(property="category_name", type="string", example="Ev Layihələri")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No individual projects found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No individual projects found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "individual_projects_{$languageId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($languageId) {
            $projects = IndividualProject::active()
                ->ordered()
                ->with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }, 'category.translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->get();
            
            if ($projects->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No individual projects found'
                ], 404);
            }
            
            $data = $projects->map(function ($project) {
                $translation = $project->translates->first();
                $categoryTranslation = $project->category->translates->first();
                
                return [
                    'id' => $project->id,
                    'title' => $translation ? $translation->title : null,
                    'description' => $translation ? $translation->description : null,
                    'image_url' => $project->image ? asset('storage/' . $project->image) : null,
                    'category_name' => $categoryTranslation ? $categoryTranslation->title : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Individual projects retrieved successfully',
                'data' => $data
            ]);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/individual-projects/{id}",
     *     summary="Get individual project by ID",
     *     description="Returns specific individual project with translated content based on header language",
     *     tags={"Individual Projects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Individual project retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Individual project retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Ev Layihəsi"),
     *                 @OA\Property(property="description", type="string", example="Müasir ev layihəsi"),
     *                 @OA\Property(property="image_url", type="string", example="https://example.com/storage/projects/project1.jpg"),
     *                 @OA\Property(property="category_name", type="string", example="Ev Layihələri"),
     *                 @OA\Property(property="seo_title", type="string", example="SEO Title"),
     *                 @OA\Property(property="seo_keywords", type="string", example="seo, keywords"),
     *                 @OA\Property(property="seo_description", type="string", example="SEO Description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Individual project not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Individual project not found")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "individual_project_{$id}_{$languageId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($id, $languageId) {
            $project = IndividualProject::with(['translates' => function($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }, 'category.translates' => function($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }])
            ->find($id);
            
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Individual project not found'
                ], 404);
            }
            
            $translation = $project->translates->first();
            $categoryTranslation = $project->category->translates->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Individual project retrieved successfully',
                'data' => [
                    'id' => $project->id,
                    'title' => $translation ? $translation->title : null,
                    'description' => $translation ? $translation->description : null,
                    'image_url' => $project->image ? asset('storage/' . $project->image) : null,
                    'category_name' => $categoryTranslation ? $categoryTranslation->title : null,
                    'seo_title' => $translation ? $translation->seo_title : null,
                    'seo_keywords' => $translation ? $translation->seo_keywords : null,
                    'seo_description' => $translation ? $translation->seo_description : null,
                ]
            ]);
        });
    }
} 