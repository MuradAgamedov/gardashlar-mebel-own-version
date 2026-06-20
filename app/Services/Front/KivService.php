<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\KivRepositoryInterface;
use App\Interfaces\Services\Front\KivServiceInterface;
use App\Services\BaseService;

class KivService extends BaseService implements KivServiceInterface
{
    protected KivRepositoryInterface $kivRepository;

    public function __construct(KivRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->kivRepository = $repository;
    }
}
