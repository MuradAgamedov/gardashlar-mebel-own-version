<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\AboutRepositoryInterface;
use App\Interfaces\Services\Front\AboutServiceInterface;
use App\Services\BaseService;

class AboutService extends BaseService implements AboutServiceInterface
{
    protected AboutRepositoryInterface $aboutRepository;

    public function __construct(AboutRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->aboutRepository = $repository;
    }
}
