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
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="product_category",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="status", type="boolean", example=true),
     *                         @OA\Property(property="image", type="string", nullable=true, example="categories/category1.jpg"),
     *                         @OA\Property(property="title", type="string", example="Electronics"),
     *                         @OA\Property(property="slug", type="string", example="electronics"),
     *                         @OA\Property(property="description", type="string", nullable=true, example="Electronic products category")
     *                     )
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
                            'id' => $homePageCategory->id,
                            'order' => $homePageCategory->order,
                            'status' => $homePageCategory->status,
                            'product_category' => [
                                'id' => $productCategory->id,
                                'parent_id' => $productCategory->parent_id,
                                'order' => $productCategory->order,
                                'status' => $productCategory->status,
                                'image' => $productCategory->image,
                                'title' => $translate->title ?? null,
                                'slug' => $translate->slug ?? null,
                                'description' => $translate->description ?? null,
                            ]
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

    /**
     * @OA\Get(
     *     path="/api/homepage-categories/{id}",
     *     operationId="getHomePageCategory",
     *     tags={"HomePageCategories"},
     *     summary="Get a specific homepage category",
     *     description="Returns a specific homepage category with its product category details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="HomePageCategory ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
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
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="product_category",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(property="image", type="string", nullable=true, example="categories/category1.jpg"),
     *                     @OA\Property(property="title", type="string", example="Electronics"),
     *                     @OA\Property(property="slug", type="string", example="electronics"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Electronic products category")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="HomePageCategory not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="HomePageCategory not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving homepage category")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:homepage-categories:{$id}:lang:{$languageId}";

            $homePageCategory = Cache::rememberForever($cacheKey, function () use ($id, $languageId) {
                return HomePageCategory::active()
                    ->with(['productCategory.translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->find($id);
            });

            if (!$homePageCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'HomePageCategory not found',
                    'data' => null
                ], 404);
            }

            $productCategory = $homePageCategory->productCategory;
            $translate = $productCategory->translates->first();

            $data = [
                'id' => $homePageCategory->id,
                'order' => $homePageCategory->order,
                'status' => $homePageCategory->status,
                'product_category' => [
                    'id' => $productCategory->id,
                    'parent_id' => $productCategory->parent_id,
                    'order' => $productCategory->order,
                    'status' => $productCategory->status,
                    'image' => $productCategory->image,
                    'title' => $translate->title ?? null,
                    'slug' => $translate->slug ?? null,
                    'description' => $translate->description ?? null,
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving homepage category: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
} 