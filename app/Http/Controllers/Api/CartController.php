<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartItemResource;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Traits\LocaleFromHeader;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use LocaleFromHeader;

    public function addCart(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('You must be logged in to complete this transaction!');
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
        

        return $this->success($cart, 'Product added to cart successfully!');
    }

    public function favoriteProductsAddToCart(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('You must be logged in to complete this transaction!');
        }

        $items = $request->all();

        if (!is_array($items) || empty($items)) {
            return $this->error('Request payload must be a non-empty array of items.', 422);
        }

        $validator = Validator::make($items, [
            '*.product_id' => 'required|integer|exists:products,id',
            '*.color_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $addedItems = [];

        foreach ($items as $item) {
            // Əgər bu istifadəçi üçün bu məhsul artıq cart-dadırsa, onu dəyişmə, sadəcə keç
            $cart = Cart::where('user_id', $user->id)
                ->where('product_id', $item['product_id'])
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'color_id' => $item['color_id'] ?? null,
                    'count' => 1,
                ]);
            }

            $addedItems[] = $cart;
        }

        // Cari istifadəçinin cart-indakı item sayını hesabla
        $cartLength = $user->cart()->count();

        return $this->success([
            'cart' => $addedItems,
            'length' => $cartLength,
        ], 'Favorite products added to cart successfully!');
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

        // 3️⃣ Cavabı qaytar
        $request->attributes->set('languageId', $languageId);
        $request->attributes->set('favoritesMap', $favoritesMap);
        $request->attributes->set('compareIds', $compareProducts);

        return $this->success(CartItemResource::collection($carts->filter(fn($c) => $c->product)->values()), 'Cart fetched successfully');
    }
    
    
    

    public function removeCart(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('You must be logged in!');
        }

        $deleted = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->delete();

        return $this->success(null, $deleted ? 'Product removed from cart successfully!' : 'Product not found in cart!');
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
    
            return $this->success($productsData);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? []), 400);
        } catch (\Exception $e) {
            return $this->serverError('Error checking compare products: ' . $e->getMessage());
        }
    }
    
    
    

}
