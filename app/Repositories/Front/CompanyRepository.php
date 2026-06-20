<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\CompanyRepositoryInterface;
use App\Models\Company;
use App\Repositories\BaseRepository;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }
}
