<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Traits\LocaleFromHeader;
use App\Models\Product;
use App\Models\ProductCategory;

class CartController extends Controller
{
    use LocaleFromHeader;

    public function addCart(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to complete this transaction!"
            ], 401);
        }

        // Cart əlavə et və ya yenilə
        $cart = Cart::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
            ],
            [
                'count' => $request->count,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'data' => $cart
        ]);
    }

    public function getCart(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        $carts = $user->cart()->with(['product.projectCategory', 'product.images'])->get();

        $cartData = $carts->map(function ($cart) use ($languageId) {
            return [
                'cart_id' => $cart->id,
                'count' => $cart->count,
                'product' => $cart->product ? $cart->product->apiData($languageId) : null,
                'color_id' => $cart->color_id,
            ];
        });

        return response()->json([
            'success' => true,
            "data" => $cartData,
            "message" => true
        ]);
    }

    public function removeCart(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in!"
            ], 401);
        }

        $deleted = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Product removed from cart successfully!' : 'Product not found in cart!'
        ]);
    }



    public function checkCardProducts(Request $request)
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
    
            // İstifadəçi yoxlanışı
            if ($user) {
                $compareProductIds = ProductCompare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            } else {
                // Guest üçün sadəcə gələn id-ləri yoxlayırıq
                $compareProductIds = $productIds;
            }
    
            // Məhsulları DB-dən götür
            $existingProducts = Product::active()
                ->whereIn('id', $compareProductIds)
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates' => fn($q) => $q->where('language_id', $languageId),
                    'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'details.translates',
                    'detailValues.translates',
                ])
                ->get();
    
            // Map to frontend-friendly structure
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
    
                // Main image
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
                // Category & parent category
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
                    'color' => $image->color ? [
                        'id' => $image->color->id,
                        'color_code' => $image->color->color_code
                    ] : null
                ])->sortBy('order')->values();
    
                // Discount price
                $discountPrice = $product->discount_percent > 0
                    ? round($product->price - ($product->price * $product->discount_percent / 100), 2)
                    : null;
    
                return [
                    'id' => $product->id,
                    'slug' => $translation?->slug ?? '',
                    'title' => $translation?->title ?? '',
                    'available_colors' => $availableColors,
                    'category_slug' => $categorySlug,
                    'category_title' => $categoryTitle,
                    'parent_slug' => $parentSlug,
                    'parent_title' => $parentTitle,
                    'discount_price' => $discountPrice,
                    'discount_percent' => $product->discount_percent,
                    'price' => $product->price,
                    'images' => $imagesWithColors,
                    'product_details' => $product->details ?? collect(),
                    'product_detail_values' => $product->detailValues ?? collect(),
                    'product_types' => $productTypes,
                    'view_count' => $product->view_count
                ];
            });
    
            return response()->json([
                'success' => true,
                'data' => $productsData,
                'message' => true
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? [])
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error checking compare products: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    

}
