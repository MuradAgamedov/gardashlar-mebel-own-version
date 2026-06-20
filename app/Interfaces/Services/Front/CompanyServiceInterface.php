<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use App\Models\Company;

interface CompanyServiceInterface extends BaseServiceInterface
{
    public function getActiveBySlug(string $slug): ?Company;
}
