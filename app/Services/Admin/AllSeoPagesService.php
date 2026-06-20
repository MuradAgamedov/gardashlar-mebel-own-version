<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AllSeoPagesRepositoryInterface;
use App\Interfaces\Services\Admin\AllSeoPagesServiceInterface;
use App\Services\BaseService;

class AllSeoPagesService extends BaseService implements AllSeoPagesServiceInterface
{
    protected AllSeoPagesRepositoryInterface $AllSeoPagesRepository;

    public function __construct(AllSeoPagesRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->AllSeoPagesRepository = $repository;
    }
}
