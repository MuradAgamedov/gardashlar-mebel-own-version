<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CompanyRepositoryInterface;
use App\Models\Company;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Company $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
