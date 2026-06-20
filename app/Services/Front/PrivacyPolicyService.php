<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\PrivacyPolicyRepositoryInterface;
use App\Interfaces\Services\Front\PrivacyPolicyServiceInterface;
use App\Services\BaseService;

class PrivacyPolicyService extends BaseService implements PrivacyPolicyServiceInterface
{
    protected PrivacyPolicyRepositoryInterface $privacyPolicyRepository;

    public function __construct(PrivacyPolicyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->privacyPolicyRepository = $repository;
    }
}
