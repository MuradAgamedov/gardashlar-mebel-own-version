<?php
namespace App\Repositories;

use App\Models\ProductCategory;
use App\Repositories\Global\BaseRepository;

class ProductCategoryRepository extends BaseRepository
{

    public function __construct(public ProductCategory $productCategory)
    {
    }

    // Kateqoriyaları filtrlə və gətir
    public function getCategories($request)
    {
        $query = $this->productCategory->with(['translates.language', 'parent.translates.language', 'children.translates.language']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            
            // Get categories that match the search criteria
            $matchingCategoryIds = $this->productCategory->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('seo_title', 'like', "%{$search}%");
            })->pluck('id');
            
            // Get parent IDs of matching subcategories
            $parentIds = $this->productCategory->whereIn('id', $matchingCategoryIds)
                ->whereNotNull('parent_id')
                ->pluck('parent_id');
            
            // Combine matching categories and their parents
            $allMatchingIds = $matchingCategoryIds->merge($parentIds)->unique();
            
            $query->whereIn('id', $allMatchingIds);
        }
        
        return $query->ordered()->get();
    }

    // Bütün kateqoriyaları gətir
    public function getAllCategories()
    {
        return $this->productCategory->with('translates.language')->ordered()->get();
    }

    // Root kateqoriyaları gətir
    public function getRootCategories()
    {
        return $this->productCategory->with('translates.language')->root()->ordered()->get();
    }

    // Müəyyən kateqoriyadan başqa root kateqoriyaları gətir
    public function getRootCategoriesExcept($categoryId)
    {
        return $this->productCategory->with('translates.language')
            ->where('id', '!=', $categoryId)
            ->root()
            ->ordered()
            ->get();
    }
}
