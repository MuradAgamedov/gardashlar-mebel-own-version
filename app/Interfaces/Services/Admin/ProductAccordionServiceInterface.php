<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductAccordionServiceInterface extends BaseServiceInterface
{
    //

    public function getProductAccordions($product);

    public function createProductAccordion($product, array $data, array $translations);

    public function updateProductAccordion(\App\Models\ProductAccordion $accordion, array $data, array $translations);

    public function deleteProductAccordion(\App\Models\ProductAccordion $accordion);

    public function reorderProductAccordions(array $items);
}
