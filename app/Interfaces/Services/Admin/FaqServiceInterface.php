<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface FaqServiceInterface extends BaseServiceInterface
{
    //

    public function getFaqs($request);

    public function createFaq(array $data, array $translations);

    public function updateFaq(\App\Models\Faq $faq, array $data, array $translations);

    public function deleteFaq(\App\Models\Faq $faq);

    public function reorderFaqs(array $faqs);

    public function deleteSelected(array $ids);
}
