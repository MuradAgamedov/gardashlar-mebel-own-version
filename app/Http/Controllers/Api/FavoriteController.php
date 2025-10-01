<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;
use App\Traits\LocaleFromHeader;

class FavoriteController extends Controller
{
    use LocaleFromHeader;

    /**
     * Favorit əlavə et
     */
    public function addFavorite(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to complete this transaction!"
            ], 401);
        }

        // Favorite əlavə et və ya varsa təkrar etmə
        $favorite = Favorite::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
            ],
            []
        );

        return response()->json([
            'success' => true,
            'message' => 'Product added to favorites successfully!',
            'data' => $favorite
        ]);
    }

    /**
     * Favoritləri gətir
     */
    public function getFavorites(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in!"
            ], 401);
        }

        $favorites = $user->favorites()->with(['product.projectCategory', 'product.images'])->get();

        $favoriteData = $favorites->map(function ($fav) use ($languageId) {
            return [
                'favorite_id' => $fav->id,
                'product' => $fav->product ? $fav->product->apiData($languageId) : null,
                'color_id' => $fav->color_id,
            ];
        });

        return response()->json([
            'success' => true,
            "data" => $favoriteData,
            "message" => true
        ]);
    }

    /**
     * Favoriti sil
     */
    public function removeFavorite(Request $request)
    {
        $user = $request->user();

        $deleted = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Favorite removed successfully!' : 'Favorite not found!'
        ]);
    }




     /**
     * @OA\Post(
     *     path="/api/products/favorites/check",
     *     operationId="checkFavoriteProducts",
     *     tags={"Products"},
     *     summary="Check if products exist (for non-registered users)",
     *     description="Check if provided product IDs exist and are active",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3, 4}, description="Array of product IDs to check")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="existing_products", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="non_existing_products", type="array", @OA\Items(type="integer"), example={4}),
     *                 @OA\Property(property="total_requested", type="integer", example=4),
     *                 @OA\Property(property="total_existing", type="integer", example=3),
     *                 @OA\Property(property="total_non_existing", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function checkCompareProducts(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);
    
            $productIds = $request->input('product_ids', []);
            $user = $request->user();
            $languageId = $this->getLanguageIdFromHeader($request);
    
            if ($user) {
                // Qeydiyyatlı istifadəçi üçün compare DB-dən
                $compareProductIds = ProductCompare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            } else {
                // Qeydə alınmamış istifadəçi üçün session
                $guestCompare = $request->session()->get('guest_compare', []);
                $compareProductIds = array_intersect($guestCompare, $productIds);
            }
    
            // Məhsulları götür
            $existingProducts = Product::active()
                ->whereIn('id', $compareProductIds)
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates' => fn($q) => $q->where('language_id', $languageId),
                    'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                ])
                ->get();
    
            // Tapılmayan məhsullar
            $existingProductIds = $existingProducts->pluck('id')->toArray();
            $nonExistingProducts = array_diff($productIds, $existingProductIds);
    
            // Format məhsulların məlumatı
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
    
                $mainImage = $product->images->where('is_main', true)->first();
                if (!$mainImage) $mainImage = $product->images->first();
    
                $categorySlug = null;
                $parentSlug = null;
    
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
    
                    if ($category->parent_id) {
                        $parentCategory = ProductCategory::find($category->parent_id);
                        if ($parentCategory) {
                            $parentTranslation = $parentCategory->translates()->where('language_id', $languageId)->first();
                            $parentSlug = $parentTranslation ? $parentTranslation->slug : null;
                        }
                    }
                }
    
                $productTypes = $product->types->map(function ($type) {
                    $translation = $type->translates->first();
                    return [
                        'id' => $type->id,
                        'title' => $translation ? $translation->title : ''
                    ];
                });
    
                $availableColors = $product->colors->map(function($color) {
                    return [
                        'id' => $color->id,
                        'color_code' => $color->color_code
                    ];
                });
    
                $imagesWithColors = $product->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                        'is_main' => $image->is_main,
                        'order' => $image->order,
                        'color' => $image->color ? [
                            'id' => $image->color->id,
                            'color_code' => $image->color->color_code
                        ] : null
                    ];
                })->sortBy('order')->values();
    
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'view_count' => $product->view_count,
                    'discount_percent' => $product->discount_percent,
                    'category_slug' => $categorySlug,
                    'parentSlug' => $parentSlug,
                    'product_types' => $productTypes,
                    'available_colors' => $availableColors,
                    'images' => $imagesWithColors
                ];
            });
    
            return response()->json([
                'success' => true,
                'data' => $productsData
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? []),
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Məhsulları yoxlayarkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    

 
}
