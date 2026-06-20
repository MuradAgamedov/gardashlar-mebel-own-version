<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\WarrantyPolicyRepositoryInterface;
use App\Interfaces\Services\Front\WarrantyPolicyServiceInterface;
use App\Services\BaseService;

class WarrantyPolicyService extends BaseService implements WarrantyPolicyServiceInterface
{
    protected WarrantyPolicyRepositoryInterface $warrantyPolicyRepository;

    public function __construct(WarrantyPolicyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->warrantyPolicyRepository = $repository;
    }
}
