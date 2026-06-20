<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\KivRepositoryInterface;
use App\Interfaces\Services\Admin\KivServiceInterface;
use App\Services\BaseService;

class KivService extends BaseService implements KivServiceInterface
{
    protected KivRepositoryInterface $KivRepository;

    public function __construct(KivRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->KivRepository = $repository;
    }
}
