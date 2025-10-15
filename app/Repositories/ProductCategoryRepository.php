<?php
namespace App\Repositories;

use App\Models\ProductCategory;
use App\Services\ImageService;
use App\Repositories\Global\BaseRepository;

class ProductCategoryRepository extends BaseRepository
{
    public function __construct(
        public ProductCategory $productCategory,
        protected ImageService $imageService
    )
    {
    }

    public function getCategories($request)
    {
        $query = $this->productCategory->with(['translates.language', 'parent.translates.language', 'children.translates.language']);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            
            $matchingCategoryIds = $this->productCategory->whereHas('translates', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('seo_title', 'like', "%{$search}%");
            })->pluck('id');
            
            $parentIds = $this->productCategory->whereIn('id', $matchingCategoryIds)
                ->whereNotNull('parent_id')
                ->pluck('parent_id');
            
            $allMatchingIds = $matchingCategoryIds->merge($parentIds)->unique();
            
            $query->whereIn('id', $allMatchingIds);
        }
        
        return $query->ordered()->get();
    }

    public function getAllCategories()
    {
        return $this->productCategory->with('translates.language')->ordered()->get();
    }

    public function getRootCategories()
    {
        return $this->productCategory->with('translates.language')->root()->ordered()->get();
    }

    public function getRootCategoriesExcept($categoryId)
    {
        return $this->productCategory->with('translates.language')
            ->where('id', '!=', $categoryId)
            ->root()
            ->ordered()
            ->get();
    }

    public function createProductCategory(array $data, array $translates, $imageFile)
    {
        $imagePath = null;
        if ($imageFile) {
            $imagePath = $this->imageService->upload($imageFile, 'product-categories');
        }

        $category = $this->productCategory->create([
            'parent_id' => $data['parent_id'],
            'status' => $data['status'],
            'image' => $imagePath,
        ]);

        foreach ($translates as $translate) {
            $category->translates()->create([
                'language_id' => $translate['language_id'],
                'title' => trim($translate['title']),
                'slug' => trim($translate['slug']),
                'seo_title' => trim($translate['seo_title'] ?? ''),
                'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                'seo_description' => trim($translate['seo_description'] ?? ''),
                'seo_text' => trim($translate['seo_text'] ?? ''),
                'image_alt_text' => trim($translate['image_alt_text'] ?? ''),
            ]);
        }

        return $category;
    }

    public function updateProductCategory(ProductCategory $category, array $data, array $translates, $imageFile, $removeImage)
    {
        $imagePath = $category->image;
        
        if ($removeImage) {
            if ($category->image) {
                $this->imageService->delete($category->image);
            }
            $imagePath = null;
        } elseif ($imageFile) {
            $imagePath = $this->imageService->update($imageFile, $category->image, 'product-categories');
        }

        $category->update([
            'parent_id' => $data['parent_id'],
            'status' => $data['status'],
            'image' => $imagePath,
        ]);

        foreach ($translates as $translate) {
            $category->translates()->updateOrCreate(
                ['language_id' => $translate['language_id']],
                [
                    'title' => trim($translate['title']),
                    'slug' => trim($translate['slug']),
                    'seo_title' => trim($translate['seo_title'] ?? ''),
                    'seo_keywords' => trim($translate['seo_keywords'] ?? ''),
                    'seo_description' => trim($translate['seo_description'] ?? ''),
                    'seo_text' => trim($translate['seo_text'] ?? ''),
                    'image_alt_text' => trim($translate['image_alt_text'] ?? ''),
                ]
            );
        }

        return $category;
    }

    public function deleteProductCategory(ProductCategory $category)
    {
        if ($category->image) {
            $this->imageService->delete($category->image);
        }
        return $category->delete();
    }

    public function reorderProductCategories(array $items, $type)
    {
        if ($type === 'main') {
            foreach ($items as $index => $itemId) {
                $this->productCategory->where('id', $itemId)
                    ->whereNull('parent_id')
                    ->update(['order' => $index + 1]);
            }
        } else {
            foreach ($items as $index => $itemId) {
                $this->productCategory->where('id', $itemId)
                    ->whereNotNull('parent_id')
                    ->update(['order' => $index + 1]);
            }
        }
        return true;
    }

    public function deleteSelectedCategories(array $selectedIds)
    {
        $categoriesWithChildren = $this->productCategory->whereIn('id', $selectedIds)
            ->whereHas('children')
            ->get();

        if ($categoriesWithChildren->isNotEmpty()) {
            $categoryNames = $categoriesWithChildren->map(function($category) {
                $firstTranslation = $category->translates->first();
                return $firstTranslation ? $firstTranslation->title : 'Untitled';
            })->implode(', ');
            
            throw new \Exception("Cannot delete categories with subcategories: {$categoryNames}");
        }

        $categoriesToDelete = $this->productCategory->whereIn('id', $selectedIds)->get();
        
        foreach ($categoriesToDelete as $category) {
            if ($category->image) {
                $this->imageService->delete($category->image);
            }
        }
        
        $this->productCategory->whereIn('id', $selectedIds)->delete();
        
        return count($selectedIds);
    }
}
