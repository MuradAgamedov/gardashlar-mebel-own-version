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
    
        $categorySlug = $request->query('category_slug', null);
    
        if (!$categorySlug) {
            return response()->json([
                'success' => false,
                'message' => 'category_slug not provided. Products will not be returned.',
            ]);
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
    
        // 🔹 Selected category
        $selectedCategory = $categories->firstWhere('slug', $categorySlug);
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
            ->map(function ($product) use ($languageId) {
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
                        $dTr = $d->translate($languageId)->first();
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
            $user = $request->user();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in!',
                    'data' => null
                ], 401);
            }
    
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);
    
            $productIds = $request->input('product_ids');
    
            // Yalnız istifadəçinin compare-də olan məhsulları yoxla
            $compareProductIds = ProductCompare::where('user_id', $user->id)
                ->whereIn('product_id', $productIds)
                ->pluck('product_id')
                ->toArray();
    
            if (empty($compareProductIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'existing_products' => [],
                        'non_existing_product_ids' => $productIds
                    ],
                    'message' => 'No products from your list found in compare.'
                ]);
            }
    
            $languageId = $request->header('language_id') ?? Language::getMainLanguage()->id;
    
            $existingProducts = Product::active()
                ->whereIn('id', $compareProductIds)
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates' => fn($q) => $q->where('language_id', $languageId),
                    'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code')
                ])
                ->get();
    
            $existingProductIds = $existingProducts->pluck('id')->toArray();
            $nonExistingProductIds = array_diff($productIds, $existingProductIds);
    
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();
    
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    
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
                    'order' => $image->order,
                    'color' => $image->color ? [
                        'id' => $image->color->id,
                        'color_code' => $image->color->color_code
                    ] : null
                ])->sortBy('order')->values();
    
                return [
                    'id' => $product->id,
                    'title' => $translation ? $translation->title : '',
                    'slug' => $translation ? $translation->slug : '',
                    'price' => $product->price,
                    'discount_percent' => $product->discount_percent,
                    'category_slug' => $categorySlug,
                    'parent_category_slug' => $parentSlug,
                    'product_types' => $productTypes,
                    'available_colors' => $availableColors,
                    'images' => $imagesWithColors
                ];
            });
    
            return response()->json([
                'success' => true,
                'data' => [
                    'existing_products' => $productsData,
                    'non_existing_product_ids' => array_values($nonExistingProductIds),
                ]
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? []),
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking compare products: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    
    
    
    
}
