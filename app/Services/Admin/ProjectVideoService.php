<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProjectVideoRepositoryInterface;
use App\Interfaces\Services\Admin\ProjectVideoServiceInterface;
use App\Services\BaseService;

class ProjectVideoService extends BaseService implements ProjectVideoServiceInterface
{
    protected ProjectVideoRepositoryInterface $ProjectVideoRepository;

    public function __construct(ProjectVideoRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProjectVideoRepository = $repository;
    }

    public function getVideosByProject($projectId)
    {
        return $this->ProjectVideoRepository->getVideosByProject($projectId);
    }
}
