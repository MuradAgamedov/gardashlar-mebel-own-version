<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProjectVideoRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getVideosByProject($projectId);
}
