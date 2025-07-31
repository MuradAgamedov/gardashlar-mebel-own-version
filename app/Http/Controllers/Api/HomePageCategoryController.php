<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomePageCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class HomePageCategoryController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/homepage-categories",
     *     operationId="getHomePageCategories",
     *     tags={"HomePageCategories"},
     *     summary="Get all active homepage categories",
     *     description="Returns a list of active homepage categories with their product category details, ordered by 'order' field",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language_id from languages api",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="image", type="string", nullable=true, example="categories/category1.jpg"),
     *                     @OA\Property(property="title", type="string", example="Electronics")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving homepage categories")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:homepage-categories:lang:{$languageId}";

            $homePageCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return HomePageCategory::active()
                    ->ordered()
                    ->with(['productCategory.translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get()
                    ->map(function ($homePageCategory) {
                        $productCategory = $homePageCategory->productCategory;
                        $translate = $productCategory->translates->first();
                        
                        return [
                            'id' => $productCategory->id,
                            'image' => $productCategory->image,
                            'title' => $translate->title ?? null,
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $homePageCategories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving homepage categories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


} 