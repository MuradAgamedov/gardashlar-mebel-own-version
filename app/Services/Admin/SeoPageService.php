<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SeoPageRepositoryInterface;
use App\Interfaces\Services\Admin\SeoPageServiceInterface;
use App\Services\BaseService;

class SeoPageService extends BaseService implements SeoPageServiceInterface
{
    protected SeoPageRepositoryInterface $seoPageRepository;

    public function __construct(SeoPageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->seoPageRepository = $repository;
    }

    public function getSeoPages()
    {
        return $this->seoPageRepository->getSeoPages();
    }

    public function findSeoPage($id)
    {
        return $this->seoPageRepository->findSeoPage($id);
    }

    public function getActiveCategories()
    {
        return $this->seoPageRepository->getActiveCategories();
    }

    public function createSeoPage(array $data, array $translates)
    {
        return $this->seoPageRepository->createSeoPage($data, $translates);
    }

    public function updateSeoPage($id, array $data, array $translates)
    {
        return $this->seoPageRepository->updateSeoPage($id, $data, $translates);
    }

    public function deleteSeoPage($id)
    {
        return $this->seoPageRepository->deleteSeoPage($id);
    }
}
