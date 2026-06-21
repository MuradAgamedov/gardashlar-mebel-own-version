<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorFormRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAInvestorFormServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorFormService extends BaseService implements BecomeAInvestorFormServiceInterface
{
    protected BecomeAInvestorFormRepositoryInterface $BecomeAInvestorFormRepository;

    public function __construct(BecomeAInvestorFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAInvestorFormRepository = $repository;
    }

    public function getForms($request)
    {
        return $this->BecomeAInvestorFormRepository->getForms($request);
    }

    public function deleteForm($form)
    {
        return $this->BecomeAInvestorFormRepository->deleteForm($form);
    }

    public function markAsRead($form)
    {
        return $this->BecomeAInvestorFormRepository->markAsRead($form);
    }

    public function deleteSelected(array $ids)
    {
        return $this->BecomeAInvestorFormRepository->deleteSelected($ids);
    }
}
