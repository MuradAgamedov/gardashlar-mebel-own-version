<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\RefundPolicyRepositoryInterface;
use App\Interfaces\Services\Admin\RefundPolicyServiceInterface;
use App\Services\BaseService;

class RefundPolicyService extends BaseService implements RefundPolicyServiceInterface
{
    protected RefundPolicyRepositoryInterface $RefundPolicyRepository;

    public function __construct(RefundPolicyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->RefundPolicyRepository = $repository;
    }
}
