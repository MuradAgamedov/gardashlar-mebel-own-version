<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BlogCategoryRepositoryInterface;
use App\Models\BlogCategory;
use App\Repositories\BaseRepository;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    public function __construct(BlogCategory $model)
    {
        parent::__construct($model);
    }

    public function getCategories()
    {
        return $this->GetData($this->blogCategory, ['translates.language'])->get();
    }

    public function createCategory(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {
            $category = $this->blogCategory->create([
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                BlogCategoryTranslate::create([
                    'blog_category_id' => $category->id,
                    'language_id' => $languageId,
                    'title' => trim($translation['title'] ?? ''),
                    'slug' => trim($translation['slug'] ?? ''),
                    'seo_title' => trim($translation['seo_title'] ?? ''),
                    'seo_keywords' => trim($translation['seo_keywords'] ?? ''),
                    'seo_description' => trim($translation['seo_description'] ?? ''),
                ]);
            }

            return $category->load('translates.language');
        });
    }

    public function updateCategory(BlogCategory $category, array $data, array $translations)
    {
        return DB::transaction(function () use ($category, $data, $translations) {
            $category->update([
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                BlogCategoryTranslate::updateOrCreate(
                    [
                        'blog_category_id' => $category->id,
                        'language_id' => $languageId,
                    ],
                    [
                        'title' => trim($translation['title'] ?? ''),
                        'slug' => trim($translation['slug'] ?? ''),
                        'seo_title' => trim($translation['seo_title'] ?? ''),
                        'seo_keywords' => trim($translation['seo_keywords'] ?? ''),
                        'seo_description' => trim($translation['seo_description'] ?? ''),
                    ]
                );
            }

            return $category->load('translates.language');
        });
    }

    public function deleteCategory(BlogCategory $category)
    {
        return $category->delete();
    }

    public function reorderCategories(array $categories)
    {
        return $this->UpdateOrder($this->blogCategory, $categories);
    }

    public function bulkDelete(array $ids)
    {
        return $this->blogCategory->whereIn('id', $ids)->delete();
    }
}
