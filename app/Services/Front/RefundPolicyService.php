<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\RefundPolicyRepositoryInterface;
use App\Interfaces\Services\Front\RefundPolicyServiceInterface;
use App\Services\BaseService;

class RefundPolicyService extends BaseService implements RefundPolicyServiceInterface
{
    protected RefundPolicyRepositoryInterface $refundPolicyRepository;

    public function __construct(RefundPolicyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->refundPolicyRepository = $repository;
    }
}
