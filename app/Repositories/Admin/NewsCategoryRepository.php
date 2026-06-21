<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\NewsCategoryRepositoryInterface;
use App\Models\NewsCategory;
use App\Models\NewsCategoryTranslate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class NewsCategoryRepository extends BaseRepository implements NewsCategoryRepositoryInterface
{
    public function __construct(NewsCategory $model)
    {
        parent::__construct($model);
    }

    public function getCategories()
    {
        return $this->model->newQuery()->with(['translates.language'])->get();
    }

    public function createCategory(array $data, array $translations)
    {
        return DB::transaction(function () use ($data, $translations) {
            $category = $this->model->newQuery()->create([
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                NewsCategoryTranslate::create([
                    'news_category_id'  => $category->id,
                    'language_id'       => $languageId,
                    'title'             => trim($translation['title'] ?? ''),
                    'slug'              => trim($translation['slug'] ?? ''),
                    'seo_title'         => trim($translation['seo_title'] ?? ''),
                    'seo_keywords'      => trim($translation['seo_keywords'] ?? ''),
                    'seo_description'   => trim($translation['seo_description'] ?? ''),
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
                        'language_id'      => $languageId,
                    ],
                    [
                        'title'           => trim($translation['title'] ?? ''),
                        'slug'            => trim($translation['slug'] ?? ''),
                        'seo_title'       => trim($translation['seo_title'] ?? ''),
                        'seo_keywords'    => trim($translation['seo_keywords'] ?? ''),
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
        foreach ($categories as $index => $categoryId) {
            $this->model->newQuery()->where('id', $categoryId)->update(['order' => $index + 1]);
        }
        return true;
    }

    public function bulkDelete(array $ids)
    {
        return $this->model->newQuery()->whereIn('id', $ids)->delete();
    }
}
