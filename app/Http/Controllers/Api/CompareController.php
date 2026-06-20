<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CompareCategoryResource;
use App\Http\Resources\Api\CompareProductResource;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductCompare;
use App\Traits\LocaleFromHeader;


class CompareController extends Controller
{
    use LocaleFromHeader;
    /**
     * Add a product to the user's compare list
     */
    public function addCompare(Request $request)
    {
        $user = $request->user(); // Token ilə istifadəçi

        if (!$user) {
            return $this->unauthorized();
        }


        $productId = $request->input('product_id');

        // Check if product already exists in compare
        $existing = ProductCompare::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return $this->error('Product already in compare list', 422);
        }

        // Add to compare
        ProductCompare::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        return $this->success(null, 'Product added to compare list successfully.');
    }

    /**
     * Get all products in user's compare list
     */
    public function getCompare(Request $request)
    {
        $user = $request->user();
    
        if (!$user) {
            return $this->unauthorized();
        }

        $languageId = $this->getLanguageIdFromHeader($request);
    
        $compareProductIds = ProductCompare::where('user_id', $user->id)->pluck('product_id');

        $categories = ProductCategory::whereNotNull('parent_id')
            ->whereHas('products', fn($q) => $q->whereIn('products.id', $compareProductIds)->active())
            ->active()
            ->ordered()
            ->with([
                'translates'        => fn($q) => $q->where('language_id', $languageId),
                'parent.translates' => fn($q) => $q->where('language_id', $languageId),
            ])
            ->get();

        if ($categories->isEmpty()) {
            return $this->success(['categories' => [], 'products' => [], 'selected_category' => null], 'No categories with compare products.');
        }

        $categorySlug     = $request->query('category_slug');
        $selectedCategory = $categorySlug
            ? $categories->first(fn($c) => $c->translates->first()?->slug === $categorySlug)
            : $categories->first();

        if (!$selectedCategory) {
            return $this->error('Selected category not found in compare list.', 422);
        }

        $products = Product::whereIn('id', $compareProductIds)
            ->whereHas('categories', fn($q) => $q->where('product_categories.id', $selectedCategory->id))
            ->active()
            ->ordered()
            ->with([
                'translates'                         => fn($q) => $q->where('language_id', $languageId),
                'images'                             => fn($q) => $q->where('status', true)->orderBy('order'),
                'details.translates'                 => fn($q) => $q->where('language_id', $languageId),
                'detailValues.translates'            => fn($q) => $q->where('language_id', $languageId),
                'detailValues.details.translates'    => fn($q) => $q->where('language_id', $languageId),
                'types.translates'                   => fn($q) => $q->where('language_id', $languageId),
                'categories.translates'              => fn($q) => $q->where('language_id', $languageId),
                'categories.parent.translates'       => fn($q) => $q->where('language_id', $languageId),
            ])
            ->get();

        // Batch-load colors to avoid N+1
        $allColorIds = $products->flatMap(fn($p) => $p->images->pluck('product_color_id'))->filter()->unique();
        $colorsMap   = ProductColor::whereIn('id', $allColorIds)->get()->keyBy('id');

        // Batch-load cart/fav/compare flags
        $productIds = $products->pluck('id');
        $cartIds    = Cart::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
        $favIds     = Favorite::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();
        $cmpIds     = ProductCompare::where('user_id', $user->id)->whereIn('product_id', $productIds)->pluck('product_id')->toArray();

        $request->attributes->set('colorsMap', $colorsMap);
        $request->attributes->set('cartIds', $cartIds);
        $request->attributes->set('favIds', $favIds);
        $request->attributes->set('compareIds', $cmpIds);

        return $this->success([
            'categories'        => CompareCategoryResource::collection($categories),
            'products'          => CompareProductResource::collection($products),
            'selected_category' => new CompareCategoryResource($selectedCategory),
        ], 'User compare list fetched successfully.');
    }
    

    
    

    public function checkCompareProducts(Request $request)
    {
        try {
            $languageId        = $this->getLanguageIdFromHeader($request);
            $compareProductIds = $request->product_ids;

            if (empty($compareProductIds)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No products in compare list.',
                    'data'    => ['categories' => [], 'products' => [], 'selected_category' => null],
                ]);
            }

            $categories = ProductCategory::whereNotNull('parent_id')
                ->whereHas('products', fn($q) => $q->whereIn('products.id', $compareProductIds)->active())
                ->active()
                ->ordered()
                ->with([
                    'translates'        => fn($q) => $q->where('language_id', $languageId),
                    'parent.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->get();

            if ($categories->isEmpty()) {
                return $this->success(['categories' => [], 'products' => [], 'selected_category' => null], 'No categories with compare products.');
            }

            $categorySlug     = $request->query('category_slug');
            $selectedCategory = $categorySlug
                ? $categories->first(fn($c) => $c->translates->first()?->slug === $categorySlug)
                : $categories->first();

            if (!$selectedCategory) {
                return $this->error('Selected category not found in compare list.', 422);
            }

            $products = Product::whereIn('id', $compareProductIds)
                ->whereHas('categories', fn($q) => $q->where('product_categories.id', $selectedCategory->id))
                ->active()
                ->ordered()
                ->with([
                    'translates'                      => fn($q) => $q->where('language_id', $languageId),
                    'images'                          => fn($q) => $q->where('status', true)->orderBy('order'),
                    'details.translates'              => fn($q) => $q->where('language_id', $languageId),
                    'detailValues.translates'         => fn($q) => $q->where('language_id', $languageId),
                    'detailValues.details.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates'                => fn($q) => $q->where('language_id', $languageId),
                    'categories.translates'           => fn($q) => $q->where('language_id', $languageId),
                    'categories.parent.translates'    => fn($q) => $q->where('language_id', $languageId),
                ])
                ->get();

            $allColorIds = $products->flatMap(fn($p) => $p->images->pluck('product_color_id'))->filter()->unique();
            $colorsMap   = ProductColor::whereIn('id', $allColorIds)->get()->keyBy('id');

            $request->attributes->set('colorsMap', $colorsMap);

            return $this->success([
                'categories'        => CompareCategoryResource::collection($categories),
                'products'          => CompareProductResource::collection($products),
                'selected_category' => new CompareCategoryResource($selectedCategory),
            ], 'Compare list fetched successfully.');

        } catch (\Exception $e) {
            return $this->serverError('Error checking compare products: ' . $e->getMessage());
        }
    }

    
    
    
    
    
    public function removeCompare(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('You must be logged in!');
        }

        $deleted = ProductCompare::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->delete();

        return $this->success(null, $deleted ? 'Product removed from compare successfully!' : 'Product not found in compare list!');
    }


    public function resetCompare(Request $request)
    {
        $user = $request->user();
    
        if (!$user) {
            return $this->unauthorized();
        }

        // İstifadəçinin bütün compare məhsullarını sil
        $deletedCount = \App\Models\ProductCompare::where('user_id', $user->id)->delete();

        return $this->success(['deleted_count' => $deletedCount], $deletedCount > 0
            ? 'All products removed from compare list successfully.'
            : 'Compare list was already empty.');
    }
    
    
}
