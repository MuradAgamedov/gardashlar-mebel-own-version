<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    public function getServiceBySlugWithRelations(string $slug, int $languageId);
}
