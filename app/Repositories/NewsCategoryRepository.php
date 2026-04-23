<?php
namespace App\Repositories;

use App\Models\NewsCategory;
use App\Models\NewsCategoryTranslate;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class NewsCategoryRepository extends BaseRepository
{
    public function __construct(public NewsCategory $newsCategory)
    {
    }

    public function getCategories()
    {
        return $this->GetData($this->newsCategory, ['translates.language'])->get();
    }

    public function createCategory(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {
            $category = $this->newsCategory->create([
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                NewsCategoryTranslate::create([
                    'news_category_id' => $category->id,
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

    public function updateCategory(NewsCategory $category, array $data, array $translations)
    {
        return DB::transaction(function () use ($category, $data, $translations) {
            $category->update([
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                NewsCategoryTranslate::updateOrCreate(
                    [
                        'news_category_id' => $category->id,
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

    public function deleteCategory(NewsCategory $category)
    {
        return $category->delete();
    }

    public function reorderCategories(array $categories)
    {
        return $this->UpdateOrder($this->newsCategory, $categories);
    }

    public function bulkDelete(array $ids)
    {
        return $this->newsCategory->whereIn('id', $ids)->delete();
    }
}
