<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsPost;

class NewsSitemapController extends Controller
{
    /**
     * Generate news sitemap
     */
    public function index(Request $request)
    {
        $news = NewsPost::with("translates.language")->where('status', true)->get();
        
        $transformedNews = $news->map(function ($newsPost) {
            $translations = [];
            
            foreach ($newsPost->translates as $translate) {
                $translations[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            return $translations;
        });
        
        return response()->json([
            'success' => true,
            'data' => $transformedNews
        ]);
    }
}
