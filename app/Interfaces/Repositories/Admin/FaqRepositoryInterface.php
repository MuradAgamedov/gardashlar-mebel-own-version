<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface FaqRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getFaqs($request);

    public function createFaq(array $data, array $translations);

    public function updateFaq(\App\Models\Faq $faq, array $data, array $translations);

    public function deleteFaq(\App\Models\Faq $faq);

    public function reorderFaqs(array $faqs);

    public function deleteSelected(array $ids);
}
