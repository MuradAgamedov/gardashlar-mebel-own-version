<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface HomePageProjectServiceInterface extends BaseServiceInterface
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
