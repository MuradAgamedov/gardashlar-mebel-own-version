<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\MapRepositoryInterface;
use App\Interfaces\Services\Admin\MapServiceInterface;
use App\Services\BaseService;

class MapService extends BaseService implements MapServiceInterface
{
    protected MapRepositoryInterface $MapRepository;

    public function __construct(MapRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->MapRepository = $repository;
    }
}
