<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProjectImageRepositoryInterface;
use App\Interfaces\Services\Admin\ProjectImageServiceInterface;
use App\Services\BaseService;

class ProjectImageService extends BaseService implements ProjectImageServiceInterface
{
    protected ProjectImageRepositoryInterface $ProjectImageRepository;

    public function __construct(ProjectImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProjectImageRepository = $repository;
    }
}
