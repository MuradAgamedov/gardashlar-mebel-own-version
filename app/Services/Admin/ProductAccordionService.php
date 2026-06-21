<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductAccordionRepositoryInterface;
use App\Interfaces\Services\Admin\ProductAccordionServiceInterface;
use App\Services\BaseService;

class ProductAccordionService extends BaseService implements ProductAccordionServiceInterface
{
    protected ProductAccordionRepositoryInterface $ProductAccordionRepository;

    public function __construct(ProductAccordionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductAccordionRepository = $repository;
    }

    public function getProductAccordions($product)
    {
        return $this->ProductAccordionRepository->getProductAccordions($product);
    }

    public function createProductAccordion($product, array $data, array $translations)
    {
        return $this->ProductAccordionRepository->createProductAccordion($product, $data, $translations);
    }

    public function updateProductAccordion(\App\Models\ProductAccordion $accordion, array $data, array $translations)
    {
        return $this->ProductAccordionRepository->updateProductAccordion($accordion, $data, $translations);
    }

    public function deleteProductAccordion(\App\Models\ProductAccordion $accordion)
    {
        return $this->ProductAccordionRepository->deleteProductAccordion($accordion);
    }

    public function reorderProductAccordions(array $items)
    {
        return $this->ProductAccordionRepository->reorderProductAccordions($items);
    }
}
