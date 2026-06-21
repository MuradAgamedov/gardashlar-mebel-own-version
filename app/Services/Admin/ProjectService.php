<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProjectRepositoryInterface;
use App\Interfaces\Services\Admin\ProjectServiceInterface;
use App\Services\BaseService;

class ProjectService extends BaseService implements ProjectServiceInterface
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->projectRepository = $repository;
    }

    public function getProjects($request)
    {
        return $this->projectRepository->getProjects($request);
    }

    public function getActiveCategoriesWithMainLanguage($mainLanguageId)
    {
        return $this->projectRepository->getActiveCategoriesWithMainLanguage($mainLanguageId);
    }

    public function getActiveCategories()
    {
        return $this->projectRepository->getActiveCategories();
    }
}
