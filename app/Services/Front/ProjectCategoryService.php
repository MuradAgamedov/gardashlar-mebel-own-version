<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ProjectCategoryRepositoryInterface;
use App\Interfaces\Services\Front\ProjectCategoryServiceInterface;
use App\Services\BaseService;

class ProjectCategoryService extends BaseService implements ProjectCategoryServiceInterface
{
    protected ProjectCategoryRepositoryInterface $projectCategoryRepository;

    public function __construct(ProjectCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->projectCategoryRepository = $repository;
    }
}
