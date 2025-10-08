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
    
        // Mövcud product varsa yeni duplicate yaratma, yalnız update et
        $favorite = Favorite::updateOrCreate(
            [
                'user_id'    => $user->id,
                'product_id' => $request->product_id,
            ],
            [
                'color_id'   => $request->color_id,
            ]
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
    
        // Eager load product with all necessary relations
        $favorites = $user->favorites()->with([
            'product.translates' => fn($q) => $q->where('language_id', $languageId),
            'product.images',
            'product.colors',
            'product.details.translates' => fn($q) => $q->where('language_id', $languageId),
            'product.types.translates' => fn($q) => $q->where('language_id', $languageId),
            'product.categories.translates' => fn($q) => $q->where('language_id', $languageId),
        ])->get();
    
        // Geri plan üçün cart və compare məlumatlarını əvvəlcədən götür
        $cartItems = $user->cart()->get()->keyBy(fn($item) => $item->product_id . '-' . $item->color_id);
        $compareIds = \App\Models\ProductCompare::whereIn('product_id', $favorites->pluck('product_id'))->pluck('product_id')->toArray();
    
        $favoriteData = $favorites->map(function ($fav) use ($languageId, $user, $cartItems, $compareIds) {
            $product = $fav->product ? $fav->product->apiData($languageId) : null;
    
            // Cart və compare status
            $cartKey = $fav->product_id . '-' . $fav->color_id;
            $inCart = $cartItems[$cartKey] ?? null;
    
            return [
                'favorite_id' => $fav->id,
                'color_id'    => $fav->color_id,
                'product'     => $product,
                'inFavorite' => [
                    'isActive' => true,
                    'color_id' => $fav->color_id,
                ],
                'inCart' => [
                    'isActive' => $inCart ? true : false,
                    'color_id' => $fav->color_id,
                    'count'    => $inCart?->count ?? 0,
                ],
                'inCompare' => in_array($fav->product_id, $compareIds),
            ];
        });
    
        return response()->json([
            'success' => true,
            "data"    => $favoriteData,
            "message" => "Favorites fetched successfully"
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
    
            // Get existing active products with relationships and translations
            $existingProducts = Product::active()
                ->whereIn('id', $productIds)
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates' => fn($q) => $q->where('language_id', $languageId),
                    'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'details.translates' => fn($q) => $q->where('language_id', $languageId),
                    'detailValues.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->get();
    
            // Map to desired response structure
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
    
                // Main image
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
                // Category & parent category (slug + title)
                $categorySlug = null;
                $categoryTitle = null;
                $parentSlug = null;
                $parentTitle = null;
    
                if ($product->categories->isNotEmpty()) {
                    $category = $product->categories->first();
                    $categoryTranslation = $category->translates->first();
                    $categorySlug = $categoryTranslation?->slug;
                    $categoryTitle = $categoryTranslation?->title;
    
                    if ($category->parent_id) {
                        $parentCategory = ProductCategory::find($category->parent_id);
                        $parentTranslation = $parentCategory?->translates()->where('language_id', $languageId)->first();
                        $parentSlug = $parentTranslation?->slug;
                        $parentTitle = $parentTranslation?->title;
                    }
                }
    
                // Product types
                $productTypes = $product->types->map(fn($type) => [
                    'id' => $type->id,
                    'title' => $type->translates->first()?->title ?? ''
                ]);
    
                // Available colors
                $availableColors = $product->colors->map(fn($color) => [
                    'id' => $color->id,
                    'color_code' => $color->color_code
                ]);
    
                // Images
                $imagesWithColors = $product->images->map(fn($image) => [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'order' => $image->order,
                    'color_id' => $image->product_color_id
                ])->sortBy('order')->values();
    
                if ($imagesWithColors->isEmpty()) {
                    $imagesWithColors = collect([
                        [
                            'id' => time(),
                            'image_url' => asset('images/default_image.webp'),
                            'is_main' => false,
                            'order' => 0,
                            'color_id' => null
                        ]
                    ]);
                }
    
                // Product details with translation
                $productDetails = $product->details->map(fn($detail) => [
                    'id' => $detail->id,
                    'keyword' => $detail->translate($languageId)?->keyword ?? '',
                    'value' => $detail->translate($languageId)?->value ?? '',
                ]);
    
            // Product detail values with translation
$productDetailValues = $product->detailValues->map(fn($value) => [
    'id' => $value->id,
    'order' => $value->order,
    // Translate model-dən gəlir
    'keyword' => $value->translate($languageId)?->keyword ?? '',
    'value' => $value->translate($languageId)?->value ?? '',
    // Lazım olsa details-i də əlavə edə bilərsən
    'details' => $value->details->map(fn($detail) => [
        'id' => $detail->id,
        'order' => $detail->order,
        'keyword' => $detail->translate($languageId)?->keyword ?? '',
        'value' => $detail->translate($languageId)?->value ?? '',
    ])
]);

                // Discount price calculation
            
                return [
                    'id' => $product->id,
                    'slug' => $translation?->slug ?? '',
                    'title' => $translation?->title ?? '',
                    'available_colors' => $availableColors,
                    'category_slug' => $categorySlug,
                    'category_title' => $categoryTitle,
                    'parent_slug' => $parentSlug,
                    'parent_title' => $parentTitle,
                    'discount_price' => $product->discount_price,
                    'price' => $product->price,
                    'images' => $imagesWithColors,
                    'product_details' => $productDetails,
                    'product_detail_values' => $productDetailValues,
                    'product_types' => $productTypes,
                    'view_count' => $product->view_count
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
