<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\CompanyManagementRepositoryInterface;
use App\Models\CompanyManagement;
use App\Repositories\BaseRepository;

class CompanyManagementRepository extends BaseRepository implements CompanyManagementRepositoryInterface
{
    public function __construct(CompanyManagement $model)
    {
        parent::__construct($model);
    }
}
