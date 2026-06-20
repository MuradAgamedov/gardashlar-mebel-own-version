<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ProjectRepositoryInterface;
use App\Interfaces\Services\Front\ProjectServiceInterface;
use App\Services\BaseService;

class ProjectService extends BaseService implements ProjectServiceInterface
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->projectRepository = $repository;
    }
}
