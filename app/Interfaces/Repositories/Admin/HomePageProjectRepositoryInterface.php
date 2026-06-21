<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface HomePageProjectRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getHomePageProjects();

    public function getProjectsWithMainLanguage($mainLanguage);

    public function getCategoriesWithMainLanguage($mainLanguage);

    public function getProjectsByCategory($categoryId, $mainLanguage);

    public function createHomePageProject(array $data);

    public function updateHomePageProject(\App\Models\HomePageProject $homePageProject, array $data);

    public function deleteHomePageProject(\App\Models\HomePageProject $homePageProject);

    public function reorderHomePageProjects(array $items);
}
