<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PrivacyPolicyRepositoryInterface;
use App\Interfaces\Services\Admin\PrivacyPolicyServiceInterface;
use App\Services\BaseService;

class PrivacyPolicyService extends BaseService implements PrivacyPolicyServiceInterface
{
    protected PrivacyPolicyRepositoryInterface $PrivacyPolicyRepository;

    public function __construct(PrivacyPolicyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PrivacyPolicyRepository = $repository;
    }
}
