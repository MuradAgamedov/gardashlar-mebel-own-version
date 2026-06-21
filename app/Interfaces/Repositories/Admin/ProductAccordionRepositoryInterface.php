<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductAccordionRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProductAccordions($product);

    public function createProductAccordion($product, array $data, array $translations);

    public function updateProductAccordion(\App\Models\ProductAccordion $accordion, array $data, array $translations);

    public function deleteProductAccordion(\App\Models\ProductAccordion $accordion);

    public function reorderProductAccordions(array $items);
}
