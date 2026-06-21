<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProjectCategoryRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProjectCategories();
}
