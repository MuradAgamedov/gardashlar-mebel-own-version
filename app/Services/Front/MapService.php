<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\MapRepositoryInterface;
use App\Interfaces\Services\Front\MapServiceInterface;
use App\Services\BaseService;

class MapService extends BaseService implements MapServiceInterface
{
    protected MapRepositoryInterface $mapRepository;

    public function __construct(MapRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->mapRepository = $repository;
    }
}
