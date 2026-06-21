<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProjectCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\ProjectCategoryServiceInterface;
use App\Services\BaseService;

class ProjectCategoryService extends BaseService implements ProjectCategoryServiceInterface
{
    protected ProjectCategoryRepositoryInterface $ProjectCategoryRepository;

    public function __construct(ProjectCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProjectCategoryRepository = $repository;
    }

    public function getProjectCategories()
    {
        return $this->ProjectCategoryRepository->getProjectCategories();
    }
}
