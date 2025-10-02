<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCompare;
use App\Models\Language;

class CompareController extends Controller
{
    /**
     * Add a product to the user's compare list
     */
    public function addCompare(Request $request)
    {
        $user = $request->user(); // Token ilə istifadəçi

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

  
        $productId = $request->input('product_id');

        // Check if product already exists in compare
        $existing = ProductCompare::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in compare list',
            ]);
        }

        // Add to compare
        ProductCompare::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to compare list successfully.',
        ]);
    }

    /**
     * Get all products in user's compare list
     */
 public function getCompare(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    $languageId = $request->header('language_id') ?? Language::getMainLanguage()->id;

    // 🔹 Compare product IDs
    $compareProductIds = ProductCompare::where('user_id', $user->id)->pluck('product_id');

    // 🔹 Categories with products in compare
    $categories = ProductCategory::whereNotNull('parent_id')
        ->whereHas('products', function ($q) use ($compareProductIds) {
            $q->whereIn('products.id', $compareProductIds)->active();
        })
        ->active()
        ->ordered()
        ->get()
        ->map(function ($category) use ($languageId) {
            $translate = $category->translate($languageId);
            return [
                'id' => $category->id,
                'title' => $translate ? $translate->title : null,
                'slug' => $translate ? $translate->slug : null,
                'parent_slug' => $category->parent ? $category->parent->translate($languageId)->slug ?? null : null,
            ];
        });

    if ($categories->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'No categories with compare products.',
            'data' => [
                'categories' => [],
                'products' => [],
                'selected_category' => null,
            ],
        ]);
    }

    // 🔹 Selected category
    $categorySlug = $request->query('category_slug');
    $selectedCategory = $categorySlug
        ? $categories->firstWhere('slug', $categorySlug)
        : $categories->first(); // göndərilməzsə ilk category

    if (!$selectedCategory) {
        return response()->json([
            'success' => false,
            'message' => 'Selected category not found in compare list.',
        ]);
    }

    // 🔹 Products in selected category
    $products = Product::whereIn('id', $compareProductIds)
        ->where('category_id', $selectedCategory['id'])
        ->active()
        ->ordered()
        ->get()
        ->map(function ($product) use ($languageId, $user) {
            $translation = $product->translate($languageId);

            // Card image
            $mainImage = $product->images()->where('status', true)->where('is_main', true)->first();
            $cardImage = $mainImage && $mainImage->image
                ? asset('storage/' . $mainImage->image)
                : asset('images/default_image.webp');
            $cardImageAltText = $mainImage && $mainImage->image_alt_text
                ? $mainImage->image_alt_text
                : null;

            // Discount
            $discountPrice = $product->discount_percent > 0
                ? $product->price - ($product->price * $product->discount_percent / 100)
                : null;

            // Details
            $details = $product->details()->ordered()->get()->map(function ($detail) use ($languageId) {
                $tr = $detail->translate($languageId);
                return [
                    'id' => $detail->id,
                    'keyword' => $tr ? $tr->keyword : null,
                    'value' => $tr ? $tr->value : null,
                ];
            });

            // Detail values + nested details
            $detailValues = $product->detailValues()->ordered()->get()->map(function ($value) use ($languageId) {
                $tr = $value->translate($languageId);

                $nestedDetails = $value->details()->ordered()->get()->map(function ($d) use ($languageId) {
                    $dTr = $d->translate($languageId);
                    return [
                        'id' => $d->id,
                        'keyword' => $dTr ? $dTr->keyword : null,
                        'value' => $dTr ? $dTr->value : null,
                    ];
                });

                return [
                    'id' => $value->id,
                    'keyword' => $tr ? $tr->keyword : null,
                    'details' => $nestedDetails,
                ];
            });

            // Category slugs
            $category = $product->category;
            $categorySlug = $category ? $category->translate($languageId)->slug ?? null : null;
            $parentCategorySlug = $category && $category->parent ? $category->parent->translate($languageId)->slug ?? null : null;

            // 🔹 inCart, inFavorite, inCompare
            $inCart = \App\Models\Cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();

            $inFavorite = \App\Models\Favorite::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();

            $inCompare = \App\Models\Compare::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();

            return [
                'id' => $product->id,
                'title' => $translation ? $translation->title : '',
                'price' => $product->price,
                'discount_price' => $discountPrice,
                'discount_percent' => $product->discount_percent,
                'card_image' => $cardImage,
                'card_image_alt_text' => $cardImageAltText,
                'details' => $details,
                'detail_values' => $detailValues,
                'category_slug' => $categorySlug,
                'parent_category_slug' => $parentCategorySlug,
                'inCart' => $inCart,
                'inFavorite' => $inFavorite,
                'inCompare' => $inCompare,
            ];
        });

    return response()->json([
        'success' => true,
        'message' => 'User compare list fetched successfully.',
        'data' => [
            'categories' => $categories,
            'products' => $products,
            'selected_category' => $selectedCategory,
        ],
    ]);
}

    
    

public function checkCompareProducts(Request $request)
{
    try {
        $languageId = $request->header('language_id') ?? Language::getMainLanguage()->id;
        $user = $request->user(); // 🔹 auth varsa götür

        // 🔹 Compare product IDs
        $compareProductIds = ProductCompare::pluck('product_id')->toArray();

        if (empty($compareProductIds)) {
            return response()->json([
                'success' => true,
                'message' => 'No products in compare list.',
                'data' => [
                    'categories' => [],
                    'products' => [],
                    'selected_category' => null,
                ]
            ]);
        }

        // 🔹 Bütün category-ləri çıxar
        $categories = ProductCategory::whereNotNull('parent_id')
            ->whereHas('products', fn($q) => $q->whereIn('products.id', $compareProductIds)->active())
            ->active()
            ->ordered()
            ->get()
            ->map(function ($category) use ($languageId) {
                $translate = $category->translate($languageId);
                return [
                    'id' => $category->id,
                    'title' => $translate ? $translate->title : null,
                    'slug' => $translate ? $translate->slug : null,
                    'parent_slug' => $category->parent ? $category->parent->translate($languageId)->slug ?? null : null,
                ];
            });

        if ($categories->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No categories with compare products.',
                'data' => [
                    'categories' => [],
                    'products' => [],
                    'selected_category' => null,
                ],
            ]);
        }

        // 🔹 Selected category
        $categorySlug = $request->query('category_slug');
        $selectedCategory = $categorySlug
            ? $categories->firstWhere('slug', $categorySlug)
            : $categories->first();

        if (!$selectedCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Selected category not found in compare list.',
            ]);
        }

        // 🔹 Products in selected category
        $products = Product::whereIn('id', $compareProductIds)
            ->whereHas('categories', fn($q) => $q->where('product_categories.id', $selectedCategory['id']))
            ->active()
            ->ordered()
            ->get()
            ->map(function ($product) use ($languageId, $user) {
                $translation = $product->translate($languageId);

                $mainImage = $product->images()->where('status', true)->where('is_main', true)->first()
                    ?? $product->images()->where('status', true)->first();

                $cardImage = $mainImage && $mainImage->image
                    ? asset('storage/' . $mainImage->image)
                    : asset('images/default_image.webp');

                $cardImageAltText = $mainImage && $mainImage->image_alt_text ? $mainImage->image_alt_text : null;

                $discountPrice = $product->discount_percent > 0
                    ? $product->price - ($product->price * $product->discount_percent / 100)
                    : null;

                $details = $product->details()->ordered()->get()->map(function ($detail) use ($languageId) {
                    $tr = $detail->translate($languageId);
                    return [
                        'id' => $detail->id,
                        'keyword' => $tr ? $tr->keyword : null,
                        'value' => $tr ? $tr->value : null,
                    ];
                });

                $detailValues = $product->detailValues()->ordered()->get()->map(function ($value) use ($languageId) {
                    $tr = $value->translate($languageId);

                    $nestedDetails = $value->details()->ordered()->get()->map(function ($d) use ($languageId) {
                        $dTr = $d->translate($languageId);
                        return [
                            'id' => $d->id,
                            'keyword' => $dTr ? $dTr->keyword : null,
                            'value' => $dTr ? $dTr->value : null,
                        ];
                    });

                    return [
                        'id' => $value->id,
                        'keyword' => $tr ? $tr->keyword : null,
                        'details' => $nestedDetails,
                    ];
                });

                $category = $product->categories->first();
                $categorySlug = $category ? $category->translate($languageId)->slug ?? null : null;
                $parentCategorySlug = $category && $category->parent
                    ? $category->parent->translate($languageId)->slug ?? null
                    : null;

                // 🔹 Əlavə: inCart, inFavorite, inCompare
                $inCart = $user ? \App\Models\Cart::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false;
                $inFavorite = $user ? \App\Models\Favorite::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false;
                $inCompare = $user ? \App\Models\Compare::where('user_id', $user->id)->where('product_id', $product->id)->exists() : false;

                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'price' => $product->price,
                    'discount_price' => $discountPrice,
                    'discount_percent' => $product->discount_percent,
                    'card_image' => $cardImage,
                    'card_image_alt_text' => $cardImageAltText,
                    'details' => $details,
                    'detail_values' => $detailValues,
                    'category_slug' => $categorySlug,
                    'parent_category_slug' => $parentCategorySlug,
                    'inCart' => $inCart,
                    'inFavorite' => $inFavorite,
                    'inCompare' => $inCompare,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Compare list fetched successfully.',
            'data' => [
                'categories' => $categories,
                'products' => $products,
                'selected_category' => $selectedCategory,
            ],
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error checking compare products: ' . $e->getMessage(),
            'data' => null
        ], 500);
    }
}

    
    
    
    
    
    public function removeCompare(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in!"
            ], 401);
        }

        $deleted = ProductCompare::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Product removed from compare successfully!' : 'Product not found in compare list!'
        ]);
    }
    
}
