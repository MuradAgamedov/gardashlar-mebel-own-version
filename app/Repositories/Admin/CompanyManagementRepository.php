<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CompanyManagementRepositoryInterface;
use App\Models\CompanyManagement;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CompanyManagementRepository extends BaseRepository implements CompanyManagementRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(CompanyManagement $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
