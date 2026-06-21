<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\FaqRepositoryInterface;
use App\Interfaces\Services\Admin\FaqServiceInterface;
use App\Services\BaseService;

class FaqService extends BaseService implements FaqServiceInterface
{
    protected FaqRepositoryInterface $faqRepository;

    public function __construct(FaqRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->faqRepository = $repository;
    }

    public function getFaqs($request)
    {
        return $this->faqRepository->getFaqs($request);
    }

    public function createFaq(array $data, array $translations)
    {
        return $this->faqRepository->createFaq($data, $translations);
    }

    public function updateFaq(\App\Models\Faq $faq, array $data, array $translations)
    {
        return $this->faqRepository->updateFaq($faq, $data, $translations);
    }

    public function deleteFaq(\App\Models\Faq $faq)
    {
        return $this->faqRepository->deleteFaq($faq);
    }

    public function reorderFaqs(array $faqs)
    {
        return $this->faqRepository->reorderFaqs($faqs);
    }

    public function deleteSelected(array $ids)
    {
        return $this->faqRepository->deleteSelected($ids);
    }
}
