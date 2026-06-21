<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CatalogRepositoryInterface;
use App\Interfaces\Services\Admin\CatalogServiceInterface;
use App\Services\BaseService;

class CatalogService extends BaseService implements CatalogServiceInterface
{
    protected CatalogRepositoryInterface $catalogRepository;

    public function __construct(CatalogRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->catalogRepository = $repository;
    }

    public function getCatalogs()
    {
        return $this->catalogRepository->getCatalogs();
    }

    public function createCatalog(array $data, array $translations, $pdfFile)
    {
        return $this->catalogRepository->createCatalog($data, $translations, $pdfFile);
    }

    public function updateCatalog(\App\Models\Catalog $catalog, array $data, array $translations, $pdfFile = null)
    {
        return $this->catalogRepository->updateCatalog($catalog, $data, $translations, $pdfFile);
    }

    public function deleteCatalog(\App\Models\Catalog $catalog)
    {
        return $this->catalogRepository->deleteCatalog($catalog);
    }

    public function reorderCatalogs(array $catalogs)
    {
        return $this->catalogRepository->reorderCatalogs($catalogs);
    }
}
