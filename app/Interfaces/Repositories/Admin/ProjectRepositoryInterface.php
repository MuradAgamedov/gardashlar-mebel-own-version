<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProjects($request);

    public function getActiveCategoriesWithMainLanguage($mainLanguageId);

    public function getActiveCategories();
}
