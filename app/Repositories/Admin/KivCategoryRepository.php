<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\KivCategoryRepositoryInterface;
use App\Models\KivCategory;
use App\Repositories\BaseRepository;

class KivCategoryRepository extends BaseRepository implements KivCategoryRepositoryInterface
{
    public function __construct(KivCategory $model)
    {
        parent::__construct($model);
    }

    public function getCategories()
    {
        return $this->model->newQuery()->with(['translates.language'])->get();
    }

    public function createKivCategory(array $data, array $translations, $languages)
    {
        $category = $this->model->newQuery()->create($data);

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];

            $category->translates()->create([
                'language_id' => $language->id,
                'title'       => trim($translationData['title'] ?? ''),
                'subtitle'    => trim($translationData['subtitle'] ?? ''),
            ]);
        }

        return $category;
    }

    public function updateKivCategory(KivCategory $kivCategory, array $data, array $translations, $languages)
    {
        $kivCategory->update($data);

        foreach ($languages as $language) {
            $translationData = $translations[$language->id] ?? [];

            $kivCategory->translates()->updateOrCreate(
                ['language_id' => $language->id],
                [
                    'title'    => trim($translationData['title'] ?? ''),
                    'subtitle' => trim($translationData['subtitle'] ?? ''),
                ]
            );
        }

        return $kivCategory;
    }

    public function deleteKivCategory(KivCategory $kivCategory)
    {
        if ($kivCategory->kivs()->count() > 0) {
            throw new \Exception('Cannot delete category that has KIVs. Please delete all KIVs first.');
        }
        return $kivCategory->delete();
    }

    public function reorderKivCategories(array $categories)
    {
        foreach ($categories as $index => $categoryId) {
            $this->model->newQuery()->where('id', $categoryId)->update(['order' => $index + 1]);
        }
        return true;
    }
}
