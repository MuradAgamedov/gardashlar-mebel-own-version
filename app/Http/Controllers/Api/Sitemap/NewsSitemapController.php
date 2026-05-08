<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsCategory;
use App\Models\NewsPost;

class NewsSitemapController extends Controller
{
    /**
     * Generate news sitemap with categories and their news slugs
     */
    public function index(Request $request)
    {
        $categories = NewsCategory::with(['translates.language', 'newsPosts.translates.language'])
            ->where('status', true)
            ->orderBy('order')
            ->get();
        
        $transformedCategories = $categories->map(function ($category) {
            // Get category slug translations
            $categorySlugTranslates = [];
            foreach ($category->translates as $translate) {
                $categorySlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            // Get news posts in this category with their slug translations
            $details = [];
            foreach ($category->newsPosts->where('status', true) as $newsPost) {
                $newsSlugTranslates = [];
                foreach ($newsPost->translates as $translate) {
                    $newsSlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
                }
                $details[] = $newsSlugTranslates;
            }
            
            return [
                'categorySlugTranslates' => $categorySlugTranslates,
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
