<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Product;

class ProductSitemapController extends Controller
{
    /**
     * Generate products sitemap with categories, parent categories and their product slugs
     */
    public function index(Request $request)
    {
        $categories = ProductCategory::with(['translates.language', 'parent.translates.language', 'products.translates.language'])
            ->where('status', true)
            ->orderBy('order')
            ->get();
        
        $transformedCategories = $categories->map(function ($category) {
            // Get category slug translations
            $categorySlugTranslates = [];
            foreach ($category->translates as $translate) {
                $categorySlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            // Get parent category slug translations
            $parentSlugTranslates = [];
            if ($category->parent) {
                foreach ($category->parent->translates as $translate) {
                    $parentSlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
                }
            }
            
            // Get products in this category with their slug translations
            $details = [];
            foreach ($category->products->where('status', true) as $product) {
                $productSlugTranslates = [];
                foreach ($product->translates as $translate) {
                    $productSlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
                }
                $details[] = $productSlugTranslates;
            }
            
            return [
                'categorySlugTranslates' => $categorySlugTranslates,
                'parentSlugTranslates' => $parentSlugTranslates,
                'details' => $details
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $transformedCategories
            ]
        ]);
    }
}
