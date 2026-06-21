<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\KivCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\KivCategoryServiceInterface;
use App\Services\BaseService;

class KivCategoryService extends BaseService implements KivCategoryServiceInterface
{
    protected KivCategoryRepositoryInterface $kivCategoryRepository;

    public function __construct(KivCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->KivCategoryRepository = $repository;
    }

    public function getCategories()
    {
        return $this->KivCategoryRepository->getCategories();
    }

    public function createKivCategory(array $data, array $translations, $languages)
    {
        return $this->KivCategoryRepository->createKivCategory($data, $translations, $languages);
    }

    public function updateKivCategory(\App\Models\KivCategory $kivCategory, array $data, array $translations, $languages)
    {
        return $this->KivCategoryRepository->updateKivCategory($kivCategory, $data, $translations, $languages);
    }

    public function deleteKivCategory(\App\Models\KivCategory $kivCategory)
    {
        return $this->KivCategoryRepository->deleteKivCategory($kivCategory);
    }

    public function reorderKivCategories(array $categories)
    {
        return $this->KivCategoryRepository->reorderKivCategories($categories);
    }
}
