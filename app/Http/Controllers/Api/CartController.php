<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Traits\LocaleFromHeader;
use App\Models\Product;

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
        $products = Product::whereIn('id', $compareProductIds)
            ->active()
            ->with([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                'details.translates' => fn($q) => $q->where('language_id', $languageId), // düzəliş
                'detailValues.translates' => fn($q) => $q->where('language_id', $languageId), // düzəliş
                'types.translates' => fn($q) => $q->where('language_id', $languageId), // düzəliş
                'colors'
            ])
            ->get()
            ->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();

                $category = $product->categories->first();
                $categoryTranslate = $category ? $category->translates->first() : null;
                $parentTranslate = $category && $category->parent_id ? $category->parent->translates->first() : null;

                return [
                    'id' => $product->id,
                    'slug' => $translation?->slug ?? '',
                    'title' => $translation?->title ?? '',
                    'available_colors' => $product->colors->map(fn($color) => [
                        'id' => $color->id,
                        'name' => $color->name,
                        'hex' => $color->hex
                    ]),
                    'category_slug' => $categoryTranslate?->slug ?? '',
                    'category_title' => $categoryTranslate?->title ?? '',
                    'parent_slug' => $parentTranslate?->slug ?? '',
                    'parent_title' => $parentTranslate?->title ?? '',
                    'discount_price' => $product->discount_price,
                    'discount_percent' => $product->discount_percent,
                    'price' => $product->price,
                    'images' => $product->images->map(fn($img) => [
                        'id' => $img->id,
                        'image_url' => $img->image ? asset('storage/' . $img->image) : null,
                        'is_main' => $img->is_main
                    ]),
                    'product_details' => $product->productDetails->map(fn($detail) => [
                        'id' => $detail->id,
                        'title' => $detail->translates->first()?->title ?? '',
                    ]),
                    'product_detail_values' => $product->productDetailValues->map(fn($val) => [
                        'id' => $val->id,
                        'value' => $val->translates->first()?->value ?? '',
                    ]),
                    'product_types' => $product->productTypes->map(fn($type) => [
                        'id' => $type->id,
                        'title' => $type->translates->first()?->title ?? '',
                    ]),
                    'view_count' => $product->view_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => true
        ]);

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
