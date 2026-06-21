<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\RefundPolicyRepositoryInterface;
use App\Interfaces\Services\Admin\RefundPolicyServiceInterface;
use App\Services\BaseService;

class RefundPolicyService extends BaseService implements RefundPolicyServiceInterface
{
    protected RefundPolicyRepositoryInterface $refundPolicyRepository;

    public function __construct(RefundPolicyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->RefundPolicyRepository = $repository;
    }

    /**
     * Get refund policy with translations
     *
     * @return \App\Models\RefundPolicy
     */
    public function getRefundPolicyWithTranslations()
    {
        return $this->RefundPolicyRepository->getRefundPolicyWithTranslations();
    }

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->RefundPolicyRepository->getActiveLanguages();
    }
}
