<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Action;

interface ActionRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveCurrentOrdered(int $languageId, ?int $companyId = null): Collection;
    public function getActiveById(int $id, int $languageId): ?Action;
}
