<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogsSitemapController extends Controller
{
    /**
     * Generate blogs sitemap with categories and their blog slugs
     */
    public function index(Request $request)
    {
        $categories = BlogCategory::with(['translates.language', 'blogPosts.translates.language'])
            ->where('status', true)
            ->orderBy('order')
            ->get();
        
        $transformedCategories = $categories->map(function ($category) {
            // Get category slug translations
            $categorySlugTranslates = [];
            foreach ($category->translates as $translate) {
                $categorySlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            // Get blog posts in this category with their slug translations
            $details = [];
            foreach ($category->blogPosts->where('status', true) as $blogPost) {
                $blogSlugTranslates = [];
                foreach ($blogPost->translates as $translate) {
                    $blogSlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
                }
                $details[] = $blogSlugTranslates;
            }
            
            return [
                'categorySlugTranslates' => $categorySlugTranslates,
                'details' => $details
            ];
        });
        
        return $this->success([
            'categories' => $transformedCategories
        ]);
    }
}
