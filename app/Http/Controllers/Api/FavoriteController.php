<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductCategory;

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




  
    public function checkFavoriteProducts(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);

            $productIds = $request->input('product_ids', []);
            
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            // Get existing active products with all relationships
            $existingProducts = Product::active()
                ->whereIn('id', $productIds)
                ->with([
                    'translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'images' => function($query) {
                        $query->where('status', true)->orderBy('order');
                    },
                    'images.color' => function($query) {
                        $query->select('product_colors.id', 'product_colors.color_code');
                    },
                    'categories.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'types.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    },
                    'colors' => function($query) {
                        $query->select('product_colors.id', 'product_colors.color_code');
                    }
                ])
                ->get();
            
            // Find non-existing products
            $existingProductIds = $existingProducts->pluck('id')->toArray();
            $nonExistingProducts = array_diff($productIds, $existingProductIds);
            
            // Format existing products data
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
                
                // Find main image - first try to find is_main=true, then fallback to first image
                $mainImage = $product->images->where('is_main', true)->first();
                if (!$mainImage) {
                    $mainImage = $product->images->first();
                }
                
                // Get category information
                $categorySlug = null;
                $parentSlug = null;
                
                if ($product->categories->isNotEmpty()) {
                    // Get the first category (assuming products have one primary category)
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation ? $categoryTranslation->slug : null;
                    
                    // Get parent slug if category has parent
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
                
                // Get available colors for this product
                $availableColors = $product->colors->map(function($color) {
                    return [
                        'id' => $color->id,
                        'color_code' => $color->color_code
                    ];
                });
                
                // Get all images with their colors
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
