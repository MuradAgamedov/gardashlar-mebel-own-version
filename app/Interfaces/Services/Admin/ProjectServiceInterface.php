<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProjectServiceInterface extends BaseServiceInterface
{
    //

    public function getProjects($request);

    public function getActiveCategoriesWithMainLanguage($mainLanguageId);

    public function getActiveCategories();
}
