<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Models\Company;

interface CompanyRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveBySlug(string $slug): ?Company;
}
