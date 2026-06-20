<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SocialNetworkRepositoryInterface;
use App\Interfaces\Services\Front\SocialNetworkServiceInterface;
use App\Services\BaseService;

class SocialNetworkService extends BaseService implements SocialNetworkServiceInterface
{
    protected SocialNetworkRepositoryInterface $socialNetworkRepository;

    public function __construct(SocialNetworkRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->socialNetworkRepository = $repository;
    }

    public function getByType(string $type)
    {
        return $this->socialNetworkRepository->getByType($type);
    }

    public function getByTypes(array $types)
    {
        return $this->socialNetworkRepository->getByTypes($types);
    }

    public function getAvailableTypes(): array
    {
        return $this->socialNetworkRepository->getAvailableTypes();
    }
}
