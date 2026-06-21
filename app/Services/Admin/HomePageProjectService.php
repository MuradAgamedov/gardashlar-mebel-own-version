<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageProjectServiceInterface;
use App\Services\BaseService;

class HomePageProjectService extends BaseService implements HomePageProjectServiceInterface
{
    protected HomePageProjectRepositoryInterface $HomePageProjectRepository;

    public function __construct(HomePageProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageProjectRepository = $repository;
    }

    public function getHomePageProjects()
    {
        return $this->HomePageProjectRepository->getHomePageProjects();
    }

    public function getProjectsWithMainLanguage($mainLanguage)
    {
        return $this->HomePageProjectRepository->getProjectsWithMainLanguage($mainLanguage);
    }

    public function getCategoriesWithMainLanguage($mainLanguage)
    {
        return $this->HomePageProjectRepository->getCategoriesWithMainLanguage($mainLanguage);
    }

    public function getProjectsByCategory($categoryId, $mainLanguage)
    {
        return $this->HomePageProjectRepository->getProjectsByCategory($categoryId, $mainLanguage);
    }

    public function createHomePageProject(array $data)
    {
        return $this->HomePageProjectRepository->createHomePageProject($data);
    }

    public function updateHomePageProject(\App\Models\HomePageProject $homePageProject, array $data)
    {
        return $this->HomePageProjectRepository->updateHomePageProject($homePageProject, $data);
    }

    public function deleteHomePageProject(\App\Models\HomePageProject $homePageProject)
    {
        return $this->HomePageProjectRepository->deleteHomePageProject($homePageProject);
    }

    public function reorderHomePageProjects(array $items)
    {
        return $this->HomePageProjectRepository->reorderHomePageProjects($items);
    }
}
