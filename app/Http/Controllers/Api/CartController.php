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
            ],
            [
                'color_id' => $request->color_id,
                'count'    => $request->count,
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
    
        // 1️⃣ Bütün sepetləri və əlaqələri yüklə
        $carts = $user->cart()->with([
            'product.details.translates',
            'product.images',
            'product.types.translates',
            'product.categories.parent.translates',
            'product.colors'
        ])->get();
    
        // 2️⃣ Favorit və müqayisə məlumatlarını öncədən çək
        $favoriteProducts = $user->favorites()->select('product_id', 'color_id')->get();
        $favoritesMap = $favoriteProducts->groupBy(fn($item) => $item->product_id . '-' . $item->color_id);
    
        $compareProducts = \App\Models\ProductCompare::select('product_id')->pluck('product_id')->toArray();
    
        // 3️⃣ Cart məlumatlarını map et
        $cartData = $carts->map(function ($cart) use ($languageId, $favoritesMap, $compareProducts) {
            $product = $cart->product;
    
            if (!$product) return null;
    
            // Əsas kateqoriyanı al (bir məhsul bir neçə kateqoriyaya bağlı ola bilər, ilkini götürürük)
            $category = $product->categories->first();
            $parentCategory = $category?->parent;
    
            // Məhsul məlumatı
            $productData = [
                'id' => $product->id,
                'title' => $product->translate($languageId)?->title ?? '',
                'slug' => $product->translate($languageId)?->slug ?? '',
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'view_count' => $product->view_count,
    
                // 🔹 Category məlumatı product-un içində
                'category_title' => $category?->translates->where('language_id', $languageId)->first()?->title ?? '',
                'category_slug' => $category?->translates->where('language_id', $languageId)->first()?->slug ?? '',
                'parent_title' => $parentCategory?->translates->where('language_id', $languageId)->first()?->title ?? '',
                'parent_slug' => $parentCategory?->translates->where('language_id', $languageId)->first()?->slug ?? '',
    
                'images' => $product->images
                    ->sortBy('order')
                    ->map(fn($image) => [
                        'id' => $image->id,
                        'image_url' => $image->image ? asset('storage/' . $image->image) : asset('images/default_image.webp'),
                        'is_main' => $image->is_main,
                        'color_id' => $image->product_color_id
                    ])->values(),
    
                'available_colors' => $product->colors->map(fn($color) => [
                    'id' => $color->id,
                    'color_code' => $color->color_code
                ]),
    
                'product_types' => $product->types->map(fn($type) => [
                    'id' => $type->id,
                    'title' => $type->translates->where('language_id', $languageId)->first()?->title ?? ''
                ]),
    
                'product_details' => $product->details->map(function ($detail) use ($languageId) {
                    $translation = $detail->translates->where('language_id', $languageId)->first();
                    return [
                        'id' => $detail->id,
                        'keyword' => $translation?->keyword ?? '',
                        'value' => $translation?->value ?? '',
                    ];
                }),
            ];
    
            $key = $cart->product_id . '-' . $cart->color_id;
    
            return [
                'cart_id' => $cart->id,
                'count' => $cart->count,
                'color_id' => $cart->color_id,
                'product' => $productData,
                'inCart' => [
                    'isActive' => true,
                    'color_id' => $cart->color_id,
                    'count' => $cart->count,
                ],
                'inFavorite' => [
                    'isActive' => isset($favoritesMap[$key]),
                    'color_id' => $cart->color_id,
                ],
                'inCompare' => in_array($cart->product_id, $compareProducts),
            ];
        })->filter();
    
        // 4️⃣ Cavabı qaytar
        return response()->json([
            'success' => true,
            'data' => $cartData->values(),
            'message' => 'Cart fetched successfully',
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
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Product removed from cart successfully!' : 'Product not found in cart!'
        ]);
    }



    public function checkCardProducts(Request $request)
    {
        try {
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);
    
            $productIds = $request->input('product_ids', []);
            $user = $request->user();
            $languageId = $this->getLanguageIdFromHeader($request);
    
            $compareProductIds = $user 
                ? ProductCompare::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray()
                : $productIds;
    
            $existingProducts = Product::active()
                ->whereIn('id', $compareProductIds)
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates' => fn($q) => $q->where('language_id', $languageId),
                    'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'details.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->get();
    
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
                $categorySlug = $categoryTitle = $parentSlug = $parentTitle = null;
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
    
                $productTypes = $product->types->map(fn($type) => [
                    'id' => $type->id,
                    'title' => $type->translates->first()?->title ?? ''
                ]);
    
                $availableColors = $product->colors->map(fn($color) => [
                    'id' => $color->id,
                    'color_code' => $color->color_code
                ]);
    
                $imagesWithColors = $product->images->map(fn($image) => [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'color_id' => $image->product_color_id
                ])->sortBy('order')->values();
    
                if ($imagesWithColors->isEmpty()) {
                    $imagesWithColors = collect([
                        [
                            'id' => time(),
                            'image_url' => asset('images/default_image.webp'),
                            'is_main' => false,
                            'color_id' => null
                        ]
                    ]);
                }
    
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
                    'product_details' => $product->details->map(function($detail) use ($languageId) {
                        $translation = $detail->translates->first();
                        return [
                            'id' => $detail->id,
                            'keyword' => $translation?->keyword ?? '',
                            'value' => $translation?->value ?? '',
                        ];
                    }),
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
