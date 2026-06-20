<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectCategory;
use App\Models\Project;

class ProjectSitemapController extends Controller
{
    /**
     * Generate projects sitemap with categories and their project slugs
     */
    public function index(Request $request)
    {
        $categories = ProjectCategory::with(['translates.language', 'projects.translates.language'])
            ->where('status', true)
            ->orderBy('order')
            ->get();
        
        $transformedCategories = $categories->map(function ($category) {
            // Get category slug translations
            $categorySlugTranslates = [];
            foreach ($category->translates as $translate) {
                $categorySlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            // Get projects in this category with their slug translations
            $details = [];
            foreach ($category->projects->where('status', true) as $project) {
                $projectSlugTranslates = [];
                foreach ($project->translates as $translate) {
                    $projectSlugTranslates[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
                }
                $details[] = $projectSlugTranslates;
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
