<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProjectVideoServiceInterface extends BaseServiceInterface
{
    //

    public function getVideosByProject($projectId);
}
